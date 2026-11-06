<?php 

 return  [

["🚀 Installation", "installation",
"<h2>🚀 Installation Guide </h2>

<p>Follow these steps to install the Deep Sync Framework:</p>

<h3>📌 Step 1</h3>
<p>Clone the project from the GitHub repository or download it as a ZIP file</p>
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
<pre> php deep migrate:install </pre> 

<h3>📌 Step 4</h3>
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
Router::get('/', [HomeController::class, 'index']);
Router::get('/about', [PageController::class, 'about']);
Router::post('/login', [AuthController::class, 'login']);
</pre>

<h3>✨ Features</h3>
<ul>
<li>Supports GET & POST methods</li>
<li>Dynamic routes (/user/{id})</li>
<li>Controller-based structure</li>
</ul>"
],

 [
    "🎨 PRANCHI Blade Engine",
    "blade-engine",

<<<HTML
<div class='blade_engine_box'>

    <div class='blade_engine_header'>
        <img style='width:300px; height:300px;' src='/brands/pra2.png' alt='PRANCHI Blade Engine' />

        <div>
            <h2>🎨 PRANCHI Blade Engine</h2>
            <p>
                Deep Sync includes a custom Blade-style engine inspired by Laravel,
                designed for speed, simplicity, and extensibility.
            </p>
        </div>
    </div>

    <h3>Syntax:</h3>

    <pre>
@extends('layouts.app')

@section('content')
    Hello {{ \$name }}
@endsection
    </pre>

    <h3>Core Features:</h3>
    <ul>
        <li>@extends layout inheritance system</li>
        <li>@section / @yield template blocks</li>
        <li>@include reusable components</li>
        <li>{{ safe output escaping }}</li>
        <li>{!! raw HTML rendering !!}</li>
        <li>Lightweight parsing engine</li>
        <li>Optimized caching support</li>
    </ul>

    <div class="meaning_card">

        <p class="meaning_subtitle">
            Next Generation Blade Engine
        </p>

        <div class="meaning_list">

            <div class="meaning_item">
                <span>P</span>
                <div>
                    <h4>Powerful</h4>
                    <p>High-speed rendering with robust template capabilities.</p>
                </div>
            </div>

            <div class="meaning_item">
                <span>R</span>
                <div>
                    <h4>Reactive</h4>
                    <p>Dynamic UI handling with a modern development workflow.</p>
                </div>
            </div>

            <div class="meaning_item">
                <span>A</span>
                <div>
                    <h4>Advanced</h4>
                    <p>Advanced directives, layouts, and reusable components.</p>
                </div>
            </div>

            <div class="meaning_item">
                <span>N</span>
                <div>
                    <h4>Native</h4>
                    <p>Seamless integration with core PHP architecture.</p>
                </div>
            </div>

            <div class="meaning_item">
                <span>C</span>
                <div>
                    <h4>Clean</h4>
                    <p>Readable syntax with a clean and maintainable structure.</p>
                </div>
            </div>

            <div class="meaning_item">
                <span>H</span>
                <div>
                    <h4>Hybrid</h4>
                    <p>A perfect blend of traditional and modern templating.</p>
                </div>
            </div>

            <div class="meaning_item">
                <span>I</span>
                <div>
                    <h4>Intelligent</h4>
                    <p>Smart caching and optimized rendering performance.</p>
                </div>
            </div>

        </div>

    </div>

</div>
HTML
],

["🗄️ Database ORM", "database-orm",
"
<h2>🗄️ Deep Sync ORM System</h2>

<p>
Deep Sync ORM is a lightweight, chainable, and PDO-based Active Record ORM.
It allows you to interact with database using clean and expressive syntax without writing raw SQL.
</p>

<hr>

<h3>🚀 Basic Usage</h3>

<pre>
User::all();
User::find(1);
User::where('email', '=', 'test@gmail.com')->first();
</pre>

<hr>

<h3>✨ Features</h3>

<ul>
<li>⚡ Active Record Pattern</li>
<li>🔒 Secure PDO Prepared Queries</li>
<li>🔗 Chainable Query Builder</li>
<li>📦 Relationship Support (hasOne, hasMany, belongsTo)</li>
<li>🔍 Search & Filtering System</li>
<li>📊 Pagination + Datatable Support</li>
<li>🧠 Hidden Fields Support</li>
<li>📈 Join & Group By Queries</li>
</ul>

<hr>

<h3>🧱 Core Query Methods</h3>

<pre>
User::query()
    ->select(['id', 'name'])
    ->where('status', '=', 'active')
    ->orderBy('id', 'DESC')
    ->limit(10)
    ->get();
</pre>

<hr>

<h3>🔎 Where Conditions</h3>

<pre>
// Basic where
User::where('id', '=', 1)->first();

// Short syntax (auto '=')
User::where('email', 'test@gmail.com')->first();
</pre>

<hr>

<h3>📦 Data Fetch Methods</h3>

<ul>
<li><code>all()</code> → Fetch all records</li>
<li><code>get()</code> → Execute built query</li>
<li><code>first()</code> → First record only</li>
<li><code>find(id)</code> → Find by primary key</li>
</ul>

<pre>
User::all();
User::find(5);
User::query()->where('id', 1)->first();
</pre>

<hr>

<h3>🧬 Relationships</h3>

<h4>➡️ hasOne</h4>

<pre>
public function profile()
{
    return \$this->hasOne(Profile::class, 'user_id');
}
</pre>

<h4>➡️ hasMany</h4>

<pre>
public function posts()
{
    return \$this->hasMany(Post::class, 'user_id');
}
</pre>

<h4>➡️ belongsTo</h4>

<pre>
public function user()
{
    return \$this->belongsTo(User::class, 'user_id');
}
</pre>

<hr>

<h3>🔗 Eager Loading (with)</h3>

<pre>
User::query()
    ->with('profile', 'posts')
    ->get();
</pre>

<hr>

<h3>🔀 Joins</h3>

<pre>
User::query()
    ->join('profiles', 'users.id', '=', 'profiles.user_id')
    ->get();
</pre>

<hr>

<h3>📊 Aggregation</h3>

<pre>
User::query()->count();
User::query()->groupBy('status')->get();
</pre>

<hr>

<h3>✏️ Insert / Update / Delete</h3>

<h4>Insert</h4>

<pre>
User::create([
    'name' => 'Deepak',
    'email' => 'test@gmail.com'
]);
</pre>

<h4>Update</h4>

<pre>
User::update(1, [
    'name' => 'Updated Name'
]);
</pre>

<h4>Delete</h4>

<pre>
User::deleteWhere('id', 1);
</pre>

<hr>

<h3>🔍 Find Helpers</h3>

<pre>
User::findById(1);
User::findByEmail('test@gmail.com');
User::findByColumn('status', 'active');
</pre>

<hr>

<h3>📊 Pagination System</h3>

<pre>
User::pagination(10);
</pre>

<hr>

<h3>📈 Datatable System (Advanced)</h3>

<pre>
User::datatable([
    'page' => 1,
    'perPage' => 10,
    'search' => 'deepak',
    'orderBy' => 'id',
    'orderDir' => 'DESC'
]);
</pre>

<hr>

<h3>🧠 Group Datatable</h3>

<pre>
User::groupDatatable([
    'groupBy' => 'status',
    'select' => 'status, COUNT(*) as total'
]);
</pre>

<hr>

<h3>🚀 Summary</h3>

<p>
Deep Sync ORM gives you a powerful Laravel-like experience in pure PHP with:
clean syntax, security, and high performance query building.
</p>
"
],

["⚡ WebSocket Setup", "websocket-setup",
"<h2>⚡ WebSocket Setup</h2>

<p>
Deep Sync provides powerful real-time communication using WebSockets.
Perfect for building modern interactive applications.
</p>

<hr>

<h3>🚀 Features</h3>
<ul>
<li>Real-time event broadcasting</li>
<li>Channel-based communication</li>
<li>Redis-powered messaging</li>
<li>Lightweight & easy integration</li>
</ul>

<hr>

<h3>📌 Use Cases</h3>
<ul>
<li>💬 Chat applications</li>
<li>🔔 Live notifications</li>
<li>📊 Real-time dashboards</li>
</ul>

<hr>

<h3>🛠 Step 1: Create Channel</h3>
<p>Generate a new WebSocket channel using CLI:</p>

<pre>
php deep make:channel User
</pre>

<p>This will automatically create a channel class and event class.</p>

<hr>

<h3>📡 Step 2: Channel Class</h3>

<pre>
&lt;?php
namespace App\\WebSockets\\Realtime\\Channels;

use App\\WebSockets\\Realtime\\Realtime;

class UserChannel extends Realtime
{
    public function send(string \$event, array \$data = []): void
    {
        \$this->broadcast('UserChannel', \$event, \$data);
    }
}
</pre>

<hr>

<h3>⚡ Step 3: Create Event</h3>
<p>Dispatch real-time events using an Event class:</p>

<pre>
&lt;?php
namespace App\\Events;

use App\\Core\\Helpers\\Realtime;

class UserEvent
{
    public static function dispatch(
        int \$inactive_users,
        int \$total_user
    ): void {

        \$realtime = new Realtime();

        \$realtime->UserChannel(
            'userUpdated',
            [
                'inactive_users' => \$inactive_users,
                'total_user' => \$total_user
            ]
        );
    }
}
</pre>

<hr>

<h3>▶️ Step 4: Start Servers</h3>

<pre>
php deep redis:serve
php deep socket:serve
</pre>

<p>Make sure Redis server is running before starting the socket server.</p>

<hr>

<h3>✅ Example Output Event</h3>

<pre>
{
  \"event\": \"userUpdated\",
  \"data\": {
    \"inactive_users\": 10,
    \"total_user\": 120
  }
}
</pre>

<hr>

<h3>💡 Tips</h3>
<ul>
<li>Keep event names consistent (camelCase recommended)</li>
<li>Use meaningful channel names</li>
<li>Validate data before broadcasting</li>
</ul>

<p>🎉 Your real-time system is now ready!</p>
"
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

["📄 Pagination System", "pagination-system",
<<<'HTML'
<h2>📄 Pagination System</h2>

<p>
Deep Sync provides a simple and flexible pagination system 
for handling large datasets efficiently.
</p>

<hr>

<h3>🚀 Basic Usage</h3>

<p>Fetch paginated data using the <code>pagination()</code> method:</p>

<pre>
$docs = Doc::pagination(10);
</pre>

<p>This will return 10 records per page.</p>

<hr>

<h3>📦 In Controller</h3>

<pre>
&lt;?php
use App\Core\Response;

public function index()
{
    $docs = Doc::pagination(10);

    // AJAX request
    if (isAjax()) {
        return Response::success('data found', [
            'docs' => $docs
        ]);
    }

    // Normal page load
    return view('docs', ['docs' => $docs]);
}
</pre>

<hr>

<h3>🎨 In Blade View</h3>

<p>Container for rendering data:</p>

<pre>
&lt;div id="docs-container"&gt;&lt;/div&gt;
</pre>

<hr>

<h3>⚡ AJAX Pagination Script</h3>

<pre>
&lt;script src="/js/ds_pagination.js"&gt;&lt;/script&gt;
&lt;script&gt;

function loadDocs(page = null) {

    if (!page) {
        page = DS_Pagination.getUrlPage();
    }

    $.ajax({
        url: '{{ route("deep.docs") }}',
        data: { page: page },

        success: function(res) {

            let docs = res.data.docs;

            renderDocs(docs.data);

            DS_Pagination.render('pagination', docs.meta, loadDocs);
        }
    });
}

$(document).ready(function () {
    loadDocs();
});

function renderDocs(docs) {

    let html = '';

    docs.forEach(doc => {

        let content = (doc.content || '').substring(0, 200);

        html += `
        &lt;div class="doc-card"&gt;
            &lt;div&gt;${content}...&lt;/div&gt;
            &lt;a href="/docs/${doc.slug}"&gt;Read More →&lt;/a&gt;
        &lt;/div&gt;
        `;
    });

    $('#docs-container').html(html);
}

&lt;/script&gt;
</pre>

<hr>

<h3>✨ Features</h3>
<ul>
<li>Automatic page detection</li>
<li>Clean URL structure (?page=2)</li>
<li>Works with AJAX & normal requests</li>
<li>Lightweight & fast</li>
</ul>

<hr>

<h3>📌 Example Output</h3>

<pre>
← 1 2 3 4 →
</pre>

<p>🎉 Pagination makes your application scalable and user-friendly.</p>
HTML
],

["📦 CLI Tools", "cli-tools",
<<<'HTML'
<h2>📦 CLI Tools</h2>

<p>
Deep Sync provides a powerful and developer-friendly CLI system 
to speed up your development workflow.
</p>

<hr>

<h3>🚀 Available Commands</h3>

<h3>🚀 CLI Commands</h3>

<pre>
# 🛠 Development
php deep make:command        → Create custom CLI command
php deep make:view           → Create a new view file
php deep make:controller     → Generate controller
php deep make:model          → Generate model
php deep make:middleware     → Create middleware

# 🗄 Database
php deep make:migration      → Create migration file
php deep migrate             → Run migrations
php deep migrate:rollback    → Rollback last migration
php deep migrate:status      → Show migration status
php deep migrate:install     → Setup DB + tables + seed
php deep seed:update         → Update seed data

# ⚡ Realtime
php deep make:event          → Create event class
php deep make:channel        → Create WebSocket channel
php deep redis:serve         → Start Redis server
php deep socket:serve        → Start WebSocket server

# 🔐 System
php deep key:generate        → Generate app key
php deep app:key             → Alternative key generator
php deep serve               → Start local server
php deep serve:status        → Check server status
</pre>

<hr>

<h3>📌 Command Categories</h3>

<h4>🛠 Development</h4>
<ul>
<li><code>make:controller</code> - Create controller</li>
<li><code>make:model</code> - Create model</li>
<li><code>make:view</code> - Create view file</li>
<li><code>make:middleware</code> - Create middleware</li>
</ul>

<h4>🗄 Database</h4>
<ul>
<li><code>migrate:install</code> - Setup database & seed data</li>
<li><code>migrate</code> - Run migrations</li>
<li><code>migrate:rollback</code> - Undo last migration</li>
<li><code>seed:update</code> - Update seed data without reinstall</li>
</ul>

<h4>⚡ Realtime</h4>
<ul>
<li><code>make:event</code> - Create event class</li>
<li><code>make:channel</code> - Create WebSocket channel</li>
<li><code>redis:serve</code> - Start Redis</li>
<li><code>socket:serve</code> - Start WebSocket server</li>
</ul>

<h4>🔐 System</h4>
<ul>
<li><code>key:generate</code> - Generate app key</li>
<li><code>serve</code> - Run local server</li>
<li><code>serve:status</code> - Check server status</li>
</ul>

<hr>

<h3>✨ Features</h3>
<ul>
<li>⚡ Fast development workflow</li>
<li>📦 Auto file generation</li>
<li>🧠 Clean command structure</li>
<li>🔄 Easy database management</li>
<li>🚀 Built-in realtime support</li>
</ul>

<hr>

<h3>💡 Example Workflow</h3>

<pre>
php deep make:model User
php deep make:controller UserController
php deep make:migration users

php deep migrate
php deep seed:update

php deep serve
</pre>

<p>🎉 Everything you need is just one command away.</p>
HTML
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



[
    "🛡️ Rate Limiting Middleware",
    "rate-limiting",
    <<<'HTMLDOC'
<h2>🛡️ Rate Limiting Middleware</h2>

<p>Deep Sync Framework provides Redis-powered Rate Limiting Middleware to control the number of requests a client can make within a specified time window.</p>

<hr>

<h3>🚀 Features</h3>
<ul>
<li>Redis-based request counting</li>
<li>Configurable maximum attempts and decay time</li>
<li>IP-based request isolation</li>
<li>Separate limits for HTTP methods and URL paths</li>
<li>Rate limit response headers</li>
<li>HTTP 429 Too Many Requests handling</li>
<li>Custom error page for Web requests</li>
<li>JSON error response for API requests</li>
<li>Fail-open behavior if Redis is unavailable</li>
</ul>

<hr>

<h3>⚡ Fluent Route Usage</h3>
<pre>
Router::post("/login", [LoginController::class, "login"])
    ->rateLimit(5, 1);
</pre>

<p>This allows 5 requests per IP address for the same HTTP method and URL path within a 1-minute window.</p>

<hr>

<h3>🧩 Middleware Syntax</h3>
<pre>
Router::post("/login", [LoginController::class, "login"])
    ->middleware("throttle:5,1");
</pre>

<p>Syntax:</p>
<pre>throttle:MAX_ATTEMPTS,DECAY_MINUTES</pre>

<hr>

<h3>⚙️ Default Configuration</h3>
<pre>
Maximum Attempts: 60
Decay Time: 1 minute
</pre>
<p>These defaults are used when parameters are omitted or invalid. Both values are normalized to a minimum of 1.</p>

<hr>

<h3>🌐 API Route Example</h3>
<pre>
Router::post("login", [AuthApiController::class, "login"])
    ->middleware("throttle:5,1");

Router::post("refresh-token", [AuthApiController::class, "refreshToken"])
    ->middleware("throttle:10,1");
</pre>

<p>API requests exceeding the limit receive a JSON error response with HTTP status code 429.</p>

<hr>

<h3>🖥️ Web Route Example</h3>
<pre>
Router::post("/login", [LoginController::class, "login"])
    ->rateLimit(5, 1);
</pre>
<p>When a Web request exceeds its limit, the framework displays the custom <code>err429</code> error page.</p>

<hr>

<h3>📨 Response Headers</h3>
<pre>
X-RateLimit-Limit: 5
X-RateLimit-Remaining: 0
Retry-After: 60
</pre>
<ul>
<li><code>X-RateLimit-Limit</code> — Configured maximum attempts.</li>
<li><code>X-RateLimit-Remaining</code> — Remaining allowed requests.</li>
<li><code>Retry-After</code> — Seconds until the current limit window expires; sent when the limit is exceeded.</li>
</ul>

<hr>

<h3>🔴 Redis Integration &amp; Fail-Open Behavior</h3>
<p>Rate limiting uses Redis to maintain request counters with automatic expiration. If Redis encounters an error, the middleware logs the error and allows the request to continue instead of blocking the application.</p>

<hr>

<h3>🔐 Request Identification</h3>
<p>Each rate-limit counter is identified using the client's IP address, HTTP request method, and URL path. Limits are therefore separate for different IP addresses, methods, and paths.</p>

<hr>

<h3>🚫 HTTP 429 Response</h3>
<ul>
<li>Web requests receive the custom 429 error page.</li>
<li>API requests receive a JSON error response.</li>
<li>The Retry-After header indicates when the current window expires.</li>
</ul>

<hr>

<h3>💡 Best Practices</h3>
<ul>
<li>Apply stricter limits to login and authentication routes.</li>
<li>Choose limits according to each endpoint's requirements.</li>
<li>Ensure Redis is configured and running for rate limiting to operate.</li>
</ul>

<p>🎉 Protect your application endpoints with Deep Sync Rate Limiting Middleware!</p>
HTMLDOC
],

[
    "🔐 Authentication Improvements",
    "authentication-improvements",
    <<<'HTMLDOC'
<h2>🔐 Authentication Improvements</h2>

<p>Deep Sync Framework provides session-based authentication for Web applications and JWT-based authentication for REST APIs.</p>

<hr>

<h3>🌐 Session-Based Authentication</h3>
<p>The built-in Auth helper manages authenticated user sessions.</p>

<pre>
use App\Config\Auth;

// Check authentication
Auth::check();

// Get authenticated user
$user = Auth::user();

// Get user details
Auth::id();
Auth::username();
Auth::avatar();
Auth::role();

// Logout
Auth::logout();
</pre>

<hr>

<h3>🔑 Login Authentication</h3>
<p>Use Auth::attempt() to verify user credentials and establish an authenticated session.</p>

<pre>
$user = Auth::attempt([
    "email" => $email,
    "password" => $password
]);

if ($user) {
    // Authentication successful
} else {
    // Invalid credentials
}
</pre>

<p>The framework verifies passwords using password_hash/password_verify-compatible hashing and regenerates the session ID after successful authentication when a session is active.</p>

<hr>

<h3>🛡️ Web Route Protection</h3>
<p>Use AuthMiddleware to protect routes that require an authenticated session.</p>

<pre>
use App\Middleware\AuthMiddleware;

Router::get("/dashboard", [DashboardController::class, "index"])
    ->middleware(AuthMiddleware::class);
</pre>

<p>Unauthenticated users are redirected to the login route.</p>

<hr>

<h3>🌍 JWT Authentication for APIs</h3>
<p>API authentication uses Bearer tokens. The login endpoint returns an access token and a refresh token.</p>

<pre>
POST /api/login

{
    "email": "user@example.com",
    "password": "your-password"
}
</pre>

<p>Send the access token in the Authorization header when accessing protected API routes.</p>

<pre>
Authorization: Bearer YOUR_ACCESS_TOKEN
</pre>

<hr>

<h3>♻️ Refresh Token</h3>
<p>Use the refresh-token endpoint to obtain a new access token. The refresh token is rotated after successful use, and its stored value is maintained as a SHA-256 hash.</p>

<pre>
POST /api/refresh-token

{
    "refresh_token": "YOUR_REFRESH_TOKEN"
}
</pre>

<p>The logout endpoint invalidates the supplied refresh token.</p>

<pre>
POST /api/logout

{
    "refresh_token": "YOUR_REFRESH_TOKEN"
}
</pre>

<hr>

<h3>⏱️ Token Expiration</h3>
<ul>
<li>Access token expiry is configured through <code>JWTConfig::EXPIRE</code> (currently 120 seconds).</li>
<li>Refresh tokens expire after 7 days.</li>
<li>Expired or invalid access tokens are rejected by JWTMiddleware.</li>
</ul>

<hr>

<h3>🔒 Security Notes</h3>
<ul>
<li>Never expose passwords, access tokens, refresh tokens, or JWT secrets.</li>
<li>Keep JWT secrets outside public source code and use secure environment configuration.</li>
<li>Use HTTPS in production to protect credentials and tokens in transit.</li>
<li>Store refresh tokens securely on the client.</li>
</ul>

<p>🎉 Build secure Web and API authentication flows with Deep Sync Framework!</p>
HTMLDOC
],

[
    "🗄️ Migration Improvements",
    "migration-improvements",
    <<<'HTMLDOC'
<h2>🗄️ Migration Improvements</h2>

<p>Deep Sync Framework provides a migration system to create, execute, and roll back database schema changes.</p>

<hr>

<h3>🚀 Create a Migration</h3>
<p>Generate a migration using the CLI:</p>

<pre>
php deep make:migration posts
</pre>

<p>The command generates a timestamped migration file. It also creates a corresponding model when one does not already exist.</p>

<hr>

<h3>🛡️ Built-in Protections</h3>
<ul>
<li>Prevents migration creation for reserved tables such as <code>users</code>, <code>password_resets</code>, and <code>migrations</code>.</li>
<li>Checks existing migration files to prevent duplicate table creation migrations.</li>
<li>Does not overwrite an existing model.</li>
</ul>

<hr>

<h3>🧱 Migration Structure</h3>
<p>A migration extends <code>Database\Migration</code> and defines <code>up()</code> and <code>down()</code> methods.</p>

<pre>
use Database\Migration;
use Database\Schema;

class Posts extends Migration
{
    public function up()
    {
        Schema::create('posts', function($table) {
            $table->id();
            $table->string("title");
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
</pre>

<hr>

<h3>⚙️ Run Migrations</h3>
<pre>
php deep migrate
</pre>
<p>The migration runner skips migrations already recorded in the migrations table. Successfully executed migrations are recorded in batches.</p>

<hr>

<h3>↩️ Rollback Migrations</h3>
<pre>
php deep migrate:rollback
</pre>
<p>Rollback executes the <code>down()</code> methods for migrations in the latest batch and removes their records after successful execution.</p>

<hr>

<h3>🧩 Available Blueprint Methods</h3>
<pre>
$table->id();
$table->int("quantity");
$table->string("title", 255);
$table->text("description");
$table->tinyint("status");
$table->enum("type", ["draft", "published"]);
$table->date("published_at");
$table->datetime("scheduled_at");

$table->nullable();
$table->default("value");
$table->unique();
$table->index();

$table->foreign("user_id")
      ->references("id")
      ->on("users")
      ->onDelete("CASCADE");

$table->timestamps();
</pre>

<p>Blueprint also supports engine, charset, collation, unsigned columns, foreign-key update/delete actions, custom foreign-key names, and column positioning with <code>after()</code>.</p>

<hr>

<h3>🗑️ Schema Helpers</h3>
<pre>
Schema::hasTable("posts");
Schema::drop("posts");
Schema::dropIfExists('posts');
</pre>

<p><code>dropIfExists()</code> temporarily disables foreign-key checks and restores the previous setting afterward.</p>

<hr>

<h3>💡 Recommended Workflow</h3>
<pre>
php deep make:migration posts
php deep migrate
</pre>

<p>Use migrations to keep database schema changes organized and version-controlled.</p>

<p>🎉 Manage database structure efficiently with Deep Sync Framework Migrations!</p>
HTMLDOC
],

];
