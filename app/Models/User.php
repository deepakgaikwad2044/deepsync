<?php
namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected static $table = "users";
    protected static $primaryKey = "id";
    protected array $hidden = ['password'];
    
    
     protected static array $searchable = [
      "name" , "email"
    ];


    /**
     * Register User (ORM)
     */
    public static function register(array $param)
    {
        $name     = trim($param['name'] ?? '');
        $email    = trim($param['email'] ?? '');
        $password = trim($param['password'] ?? '');

        if (!$name || !$email || !$password) {
            throw new \Exception("All fields are required");
        }

        // Check existing user
        if (self::query()->where('email', $email)->first()) {
            throw new \Exception("User already exists");
        }

        // Create user
        self::create([
            'name'     => $name,
            'email'    => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT),
        ]);

        // Return created user
        return self::query()->where('email', $email)->first();
    }

    /**
     * Login User (ORM)
     */
    public static function login(array $param)
{   
    $email    = trim($param['email'] ?? '');
    $password = trim($param['password'] ?? '');

    if (!$email || !$password) {
        throw new \Exception("Email and password required");
    }

    // 👇 Use RAW fetch (password included)
    $user = self::query()->where('email', $email)->firstRaw();
    

    if (!$user) {
        throw new \Exception("No such user found");
    }

    if (!password_verify($password, $user['password'])) {
        throw new \Exception("Incorrect password");
    }

    // 👇 Remove password before returning
    unset($user['password']);

    return $user;
}
}
