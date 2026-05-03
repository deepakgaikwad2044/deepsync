<?php
namespace CLI\Commands;

use App\Core\Env;
use App\Core\Console\CLI;
use PDO;

class MigrateInstall
{
    public function handle($args)
    {
        $driver = env("DB_CONNECTION");
        $host   = env("DB_HOST");
        $port   = env("DB_PORT");
        $user   = env("DB_USERNAME");
        $pass   = env("DB_PASSWORD");
        $dbname = env("DB_NAME");

        try {
            // 1️⃣ Connect without DB
            $pdo = new PDO("$driver:host=$host;port=$port", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // 2️⃣ Create DB if not exists
            $check = $pdo->query("SHOW DATABASES LIKE '$dbname'");
            if ($check->rowCount() == 0) {
                $pdo->exec("CREATE DATABASE `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                CLI::success("✅ Database '$dbname' created!\n");
            } else {
                CLI::warning("Database already exists!\n");
            }

            // 3️⃣ Connect DB
            $pdo = new PDO("$driver:host=$host;port=$port;dbname=$dbname", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // ---------------- TABLES ----------------
            $tables = [

                'migrations' => "
                    CREATE TABLE IF NOT EXISTS migrations (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        migration VARCHAR(255),
                        batch INT,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
                ",

                'users' => "
                    CREATE TABLE IF NOT EXISTS users (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        name VARCHAR(100) NOT NULL,
                        email VARCHAR(100) NOT NULL UNIQUE,
                        password VARCHAR(255) NOT NULL,
                        avtar VARCHAR(255) DEFAULT '/storage/default_avtar/default_avtar.png',
                        refresh_token VARCHAR(255) NULL,
                        refresh_token_expiry DATETIME NULL,
                        role_as TINYINT DEFAULT 0,
                        account_status TINYINT DEFAULT 1,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
                ",

                'password_resets' => "
                    CREATE TABLE IF NOT EXISTS password_resets (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        email VARCHAR(100) NOT NULL,
                        token VARCHAR(255) NOT NULL,
                        expires_at TIMESTAMP NOT NULL,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
                ",

                'docs' => "
                    CREATE TABLE IF NOT EXISTS docs (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        title VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                        slug VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL UNIQUE,
                        content LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
                "
            ];

            foreach ($tables as $name => $sql) {
                $check = $pdo->query("SHOW TABLES LIKE '$name'");
                if ($check->rowCount() > 0) {
                    CLI::warning("Table '$name' already exists!\n");
                } else {
                    $pdo->exec($sql);
                    CLI::info("✅ Table '$name' created!\n");
                }
            }

            // ---------------- DOCS SEED (UPSERT) ----------------
         

 $docs = [

["🚀 Installation", "installation",
"<h2>🚀 Installation Guide</h2>

<p>Follow these steps to install the Deep Sync Framework:</p>

<h3>📌 Step 1</h3>
<p>Clone or download the project.</p>
<p>composer create-project deepakgaikwad2044/deepsync myapp</p>

<h3>📌 Step 2</h3>
<p>Configure your .env file:</p>

<pre>
DB_HOST=localhost
DB_NAME=deepsync
DB_USER=root
DB_PASS=
</pre>

<h3>📌 Step 3</h3>
<p>Run the local server:</p>

<pre>
composer install
php deep serve
</pre>

<p>🎉 Setup complete!</p>"
],

["🧠 Routing System", "routing-system",
"<h2>🧠 Routing System</h2>

<p>The Deep Sync routing system is simple and expressive.</p>

<h3>Example:</h3>

<pre>
Route::get('/', [HomeController::class, 'index']);
Route::get('/about', [PageController::class, 'about']);
Route::post('/login', [AuthController::class, 'login']);
</pre>

<h3>✨ Features</h3>
<ul>
<li>Supports GET & POST methods</li>
<li>Dynamic routes (/user/{id})</li>
<li>Controller-based structure</li>
</ul>"
],

["🎨 Blade Engine", "blade-engine",
"<h2>🎨 Blade Engine</h2>

<p>Deep Sync includes a custom Blade engine inspired by Laravel.</p>

<h3>Syntax:</h3>

<pre>
@extends('layouts.app')

@section('content')
    Hello {{ \$name }}
@endsection
</pre>

<h3>Supported Features:</h3>
<ul>
<li>@extends layout inheritance</li>
<li>@section / @yield system</li>
<li>@include partials</li>
<li>{{ safe output }}</li>
<li>{!! raw HTML !!}</li>
</ul>"
],

["🗄️ Database ORM", "database-orm",
"<h2>🗄️ ORM System</h2>

<p>A lightweight ORM system built on PDO.</p>

<h3>Usage:</h3>

<pre>
User::all();
User::find(1);
User::where('email', 'test@gmail.com')->first();
</pre>

<h3>Features:</h3>
<ul>
<li>Active Record pattern</li>
<li>Secure PDO queries</li>
<li>Chainable methods</li>
</ul>"
],

["⚡ WebSocket Setup", "websocket-setup",
"<h2>⚡ WebSocket Setup</h2>

<p>Deep Sync provides real-time WebSocket support.</p>

<h3>Use Cases:</h3>
<ul>
<li>Chat systems 💬</li>
<li>Live notifications 🔔</li>
<li>Real-time dashboards 📊</li>
</ul>

<h3>Run server:</h3>

<pre>
php deep redis:serve
php deep socket:serve
</pre>"
],

["🔒 Security Features", "security-features",
"<h2>🔒 Security Features</h2>

<ul>
<li>XSS protection via Blade escaping</li>
<li>CSRF token support</li>
<li>SQL injection-safe ORM</li>
</ul>

<p>The framework is secure by default 💪</p>"
],

["📦 CLI Tools", "cli-tools",
"<h2>📦 CLI Tools</h2>

<p>Developer-friendly CLI system:</p>

<pre>
php deep make:model User
php deep make:migration states
php deep migrate
</pre>

<h3>Benefits:</h3>
<ul>
<li>Faster development</li>
<li>Automatic file generation</li>
<li>Reduced manual work</li>
</ul>"
],

["🌐 API System", "api-system",
"<h2>🌐 API System</h2>

<p>Built-in support for RESTful APIs:</p>

<pre>
GET /api/users
POST /api/login
PUT /api/user/1
DELETE /api/user/1
</pre>

<p>Optimized JSON responses for frontend and mobile applications 📱</p>"
],

];


            // 🔥 UPSERT Query
            $stmt = $pdo->prepare("
                INSERT INTO docs (title, slug, content)
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    title = VALUES(title),
                    content = VALUES(content)
            ");

            foreach ($docs as $doc) {
                $stmt->execute($doc);
                CLI::info("✔ Synced: " . $doc[1] . "\n");
            }

            CLI::success("📚 Docs synced successfully!\n");

        } catch (\PDOException $e) {
            CLI::error("Error: " . $e->getMessage() . "\n");
        }
    }
}