@extends("layouts.homel")

@section('content')
<section class="docs">

  <h1 class="docs-title">📘 Deep Sync Documentation</h1>

  <p class="docs-lead">
    Everything you need to get started with Deep Sync Framework.
  </p>

  <div class="docs-grid">

    <div class="doc-card">
      <h3>🚀 Installation</h3>
      <p>Setup Deep Sync in your local environment quickly.</p>
      <code>
composer create-project deepakgaikwad2044/deepsync<br><br>
composer install <br>
php deep serve
      </code>
    </div>

    <div class="doc-card">
      <h3>📂 Project Structure</h3>
      <p>Understand how files and folders are organized.</p>
      <code>
app/<br>
views/<br>
routes/<br>
public/
      </code>
    </div>

    <div class="doc-card">
      <h3>🛣️ Routing</h3>
      <p>Create routes easily for your application.</p>
      <code>
 Router::get("/", [BaseController::class, "home"])->name("home");
});
      </code>
    </div>

    <div class="doc-card">
      <h3>🎨 Blade Engine</h3>
      <p>Use layouts, sections, and components.</p>
     <code>
       @verbatim
       @include("header")
       @include("footer")
@extends('layout')<br>
@section('content')<br>
Hello World<br>
@endsection
@endverbatim
</code>
    </div>

    <div class="doc-card">

<h2>🗄️ Database ORM</h2>

<p>
Deep Sync Framework provides a powerful and flexible ORM system to interact with your database
using simple and readable syntax. No need to write raw SQL for common operations.
</p>

<hr>

<!-- ================= MODEL ================= -->

<h3>📌 Creating a Model</h3>

<pre><code>
@verbatim
use App\Core\Model;

class User extends Model {
    protected static $table = "users";
}
@endverbatim
</code></pre>

<!-- ================= BASIC ================= -->

<h3>📌 Get All Records</h3>

<pre><code>
@verbatim
$users = User::all();
@endverbatim
</code></pre>

<h3>📌 Find by ID</h3>

<pre><code>
@verbatim
$user = User::findById(1);
@endverbatim
</code></pre>

<h3>📌 Find by Column</h3>

<pre><code>
@verbatim
$user = User::query()
    ->where('email', 'test@gmail.com')
    ->first();
@endverbatim
</code></pre>

<!-- ================= WHERE ================= -->

<h3>📌 Where Conditions</h3>

<pre><code>
@verbatim
$users = User::query()
    ->where('status', 1)
    ->where('role', 'admin')
    ->get();
@endverbatim
</code></pre>

<!-- ================= SELECT ================= -->

<h3>📌 Select Specific Columns</h3>

<pre><code>
@verbatim
$users = User::query()
    ->select(['id', 'name'])
    ->get();
@endverbatim
</code></pre>

<!-- ================= ORDER ================= -->

<h3>📌 Order By</h3>

<pre><code>
@verbatim
$users = User::query()
    ->orderBy('id', 'DESC')
    ->get();
@endverbatim
</code></pre>

<!-- ================= GROUP ================= -->

<h3>📌 Group By</h3>

<pre><code>
@verbatim
$data = User::query()
    ->groupBy('role')
    ->get();
@endverbatim
</code></pre>

<!-- ================= JOIN ================= -->

<h3>📌 Join Tables</h3>

<pre><code>
@verbatim
$users = User::query()
    ->join('posts', 'users.id', '=', 'posts.user_id')
    ->get();
@endverbatim
</code></pre>

<!-- ================= INSERT ================= -->

<h3>📌 Insert Data</h3>

<pre><code>
@verbatim
User::create([
  'name' => 'Deepak',
  'email' => 'deep@gmail.com'
]);
@endverbatim
</code></pre>

<!-- ================= UPDATE ================= -->

<h3>📌 Update by ID</h3>

<pre><code>
@verbatim
User::update(1, [
  'name' => 'Updated Name'
]);
@endverbatim
</code></pre>

<h3>📌 Update by Condition</h3>

<pre><code>
@verbatim
User::updateWhere('email', 'deep@gmail.com', [
  'name' => 'New Name'
]);
@endverbatim
</code></pre>

<!-- ================= DELETE ================= -->

<h3>📌 Delete</h3>

<pre><code>
@verbatim
User::deleteWhere('id', 1);
@endverbatim
</code></pre>

<!-- ================= RELATION ================= -->

<h3>📌 Relationships</h3>

<p><strong>belongsTo</strong></p>

<pre><code>
@verbatim
class Post extends Model {

  public function user() {
    return $this->belongsTo(User::class);
  }

}

$posts = Post::query()
    ->with('user')
    ->get();
@endverbatim
</code></pre>

<!-- ================= COUNT ================= -->

<h3>📌 Count</h3>

<pre><code>
@verbatim
$total = User::query()->count();
@endverbatim
</code></pre>

<!-- ================= DATATABLE ================= -->

<h3>📌 Datatable (Pagination + Search)</h3>

<pre><code>
@verbatim
$data = User::datatable([
  "page" => 1,
  "perPage" => 10,
  "search" => "deep",
  "orderBy" => "id",
  "orderDir" => "DESC"
]);
@endverbatim
</code></pre>

<!-- ================= GROUP DATATABLE ================= -->

<h3>📌 Group Datatable</h3>

<pre><code>
@verbatim
$data = User::groupDatatable([
  "groupBy" => "role",
  "select" => "role, COUNT(*) as total",
  "searchColumns" => ["role"]
]);
@endverbatim
</code></pre>

<!-- ================= ADVANCED ================= -->

<h3>📌 Advanced Query Example</h3>

<pre><code>
@verbatim
$users = User::query()
  ->select(['users.id', 'users.name'])
  ->join('posts', 'users.id', '=', 'posts.user_id')
  ->where('users.status', 1)
  ->orderBy('users.id', 'DESC')
  ->get();
@endverbatim
</code></pre>

</div>

    <div class="doc-card">
      <h3>🔐 Authentication</h3>
      <p>Login, register, and session handling.</p>
      <code>
Auth::attempt($data);
      </code>
    </div>

  </div>

</section>
@endsection