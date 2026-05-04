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

];