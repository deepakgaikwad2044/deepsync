@extends("admin.layouts.admin")

@section("content")
<section class="admin-card">
    <h1>Admin Dashboard</h1>
    <p class="admin-muted">Welcome to Deep Sync administration.</p>

    <div class="admin-alert">
        <strong>Signed in as:</strong>
        <p>{{ $user['name'] ?? 'Admin' }}</p>
        <p class="admin-muted">{{ $user['email'] ?? '' }}</p>
    </div>

    <a class="admin-button" href="{{ route('admin.rbac.users') }}">
        Manage Roles & Permissions
    </a>

    <form action="{{ route('admin.logout') }}" method="POST">
        @csrf
        <button class="admin-button" type="submit">Logout</button>
    </form>
</section>
@endsection
