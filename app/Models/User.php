<?php
namespace App\Models;

use App\Core\Model;
use App\Config\Database;

class User extends Model
{
    protected static $table = "users";
    protected static $primaryKey = "id";

    protected array $hidden = [
        'password',
        'refresh_token',
        'refresh_token_expiry'
    ];

    protected static array $searchable = [
        "name", "email"
    ];

    /**
     * Register User (ORM)
     */
    public static function register(array $param)
    {
        $name = trim($param['name'] ?? '');
        $email = trim($param['email'] ?? '');
        $password = trim($param['password'] ?? '');

        if (!$name || !$email || !$password) {
            throw new \Exception("All fields are required");
        }

        if (self::query()->where('email', $email)->first()) {
            throw new \Exception("User already exists");
        }

        self::create([
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT),
        ]);

        return self::query()->where('email', $email)->first();
    }

    /**
     * Register a user and assign the default User role atomically.
     */
    public static function registerWithDefaultRole(array $param): array
    {
        $name = trim($param['name'] ?? '');
        $email = trim($param['email'] ?? '');
        $password = trim($param['password'] ?? '');

        if (!$name || !$email || !$password) {
            throw new \Exception("All fields are required");
        }

        $pdo = Database::connect();
        if (!$pdo instanceof \PDO) {
            throw new \RuntimeException("Database connection unavailable");
        }

        try {
            $pdo->beginTransaction();

            $check = $pdo->prepare(
                "SELECT id FROM users WHERE email = :email LIMIT 1"
            );
            $check->execute(['email' => $email]);

            if ($check->fetch(\PDO::FETCH_ASSOC)) {
                throw new \Exception("User already exists");
            }

            $insertUser = $pdo->prepare(
                "INSERT INTO users (name, email, password)
                 VALUES (:name, :email, :password)"
            );

            $insertUser->execute([
                'name' => $name,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_BCRYPT),
            ]);

            $userId = (int) $pdo->lastInsertId();

            $roleQuery = $pdo->prepare(
                "SELECT id FROM roles WHERE slug = :slug LIMIT 1"
            );
            $roleQuery->execute(['slug' => 'user']);

            $role = $roleQuery->fetch(\PDO::FETCH_ASSOC);

            if (!$role) {
                throw new \Exception("Default User role not found");
            }

            $assignRole = $pdo->prepare(
                "INSERT INTO role_user (user_id, role_id)
                 VALUES (:user_id, :role_id)"
            );

            $assignRole->execute([
                'user_id' => $userId,
                'role_id' => (int) $role['id'],
            ]);

            $userQuery = $pdo->prepare(
                "SELECT id, name, email, avtar, role_as,
                        account_status, created_at, updated_at
                 FROM users
                 WHERE id = :id
                 LIMIT 1"
            );
            $userQuery->execute(['id' => $userId]);

            $user = $userQuery->fetch(\PDO::FETCH_ASSOC);

            if (!$user) {
                throw new \Exception("Registered user could not be retrieved");
            }

            $pdo->commit();

            return $user;
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            throw $e;
        }
    }

    /**
     * Login User (ORM)
     */
    public static function login(array $param)
    {
        $email = trim($param['email'] ?? '');
        $password = trim($param['password'] ?? '');

        if (!$email || !$password) {
            throw new \Exception("Email and password required");
        }

        $user = self::query()->where('email', $email)->firstRaw();

        if (!$user) {
            throw new \Exception("No such user found");
        }

        if (!password_verify($password, $user['password'])) {
            throw new \Exception("Incorrect password");
        }

        unset($user['password']);

        return $user;
    }
}
