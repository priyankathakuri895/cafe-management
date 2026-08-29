<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Sign in') &middot; {{ config('cafe.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { min-height: 100vh; display: grid; place-items: center; background: linear-gradient(135deg, #2b2422, #4a3a2c); }
        .auth-card { width: 100%; max-width: 400px; border: none; border-radius: .8rem; box-shadow: 0 18px 40px rgba(0,0,0,.28); }
        .btn-cafe { background: #b5651d; border-color: #b5651d; color: #fff; }
        .btn-cafe:hover { background: #9d571a; border-color: #9d571a; color: #fff; }
    </style>
</head>
<body>
<div class="auth-card card p-4">
    @yield('content')
</div>
</body>
</html>
