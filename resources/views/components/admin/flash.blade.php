@if (session('success'))
    <div class="admin-flash admin-flash--success" role="status">{{ session('success') }}</div>
@endif

@if (session('error'))
    <div class="admin-flash admin-flash--error" role="alert">{{ session('error') }}</div>
@endif

@if ($errors->any())
    <div class="admin-flash admin-flash--error" role="alert">Please fix the highlighted fields below.</div>
@endif
