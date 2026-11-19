@extends("admin.layouts.admin")

@section("content")
<div class="admin-card">
    <h1>Admin Sign In</h1>
    <p class="admin-muted">Sign in with your authorized Admin account.</p>

    @flashError
    @flashSuccess

    <form action="{{ route('admin.login.verify') }}" method="POST">
        @csrf

        <div class="admin-field">
            <label for="admin-email">Email Address</label>
            <input
                id="admin-email"
                type="email"
                name="email"
                placeholder="Enter Admin email"
                value="{{ old('email') }}"
                autocomplete="username"
                required
            >
            @error('email')
                <div class="admin-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="admin-field">
            <label for="admin-password">Password</label>
            <input
                id="admin-password"
                type="password"
                name="password"
                placeholder="Enter password"
                autocomplete="current-password"
                required
            >
            @error('password')
                <div class="admin-error">{{ $message }}</div>
            @enderror
        </div>

        <button class="admin-button" type="submit">Sign In to Admin</button>
    </form>
</div>
@endsection
