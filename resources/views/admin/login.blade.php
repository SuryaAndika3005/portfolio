<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Surya Andika</title>
    @vite(['resources/css/admin.css'])
</head>
<body class="admin-body font-sans antialiased min-h-screen flex items-center justify-center px-6">

    <div class="w-full max-w-sm">
        <p class="admin-brand !p-0 !pb-1">SURYA<span>ANDIKA</span></p>
        <h1 class="text-subheading font-extrabold text-ink mb-1">Admin Login</h1>
        <p class="text-small text-muted mb-8">Manage your portfolio projects.</p>

        <form method="POST" action="{{ route('admin.login.store') }}" class="space-y-5">
            @csrf
            <x-admin.field label="Email" for="email" name="email">
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="admin-input {{ $errors->has('email') ? 'has-error' : '' }}">
            </x-admin.field>
            <x-admin.field label="Password" for="password" name="password">
                <input id="password" type="password" name="password" required class="admin-input">
            </x-admin.field>
            <label class="flex items-center gap-2 text-small text-muted">
                <input type="checkbox" name="remember" class="rounded border-border-light">
                Remember me
            </label>
            <button type="submit" class="btn btn-primary w-full justify-center">
                Sign In
            </button>
        </form>

        <a href="{{ route('home') }}" class="block text-center text-small text-muted hover:text-primary mt-6 transition-colors duration-[var(--motion-fast)]">&larr; Back to portfolio</a>
    </div>

</body>
</html>
