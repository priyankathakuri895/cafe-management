<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') &middot; {{ config('cafe.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --cm-sidebar: #22201f;
            --cm-accent: #b5651d;
            --cm-accent-soft: #f6ede4;
        }
        body { background: #f4f5f7; }
        .cm-shell { display: flex; min-height: 100vh; }
        .cm-sidebar {
            width: 245px; flex: 0 0 245px; background: var(--cm-sidebar); color: #cfc9c4;
            position: sticky; top: 0; height: 100vh; overflow-y: auto;
        }
        .cm-brand { padding: 1.15rem 1.25rem; border-bottom: 1px solid rgba(255,255,255,.08); }
        .cm-brand h1 { font-size: 1.05rem; margin: 0; color: #fff; font-weight: 600; letter-spacing: .2px; }
        .cm-brand small { color: #8d857e; }
        .cm-nav { padding: .75rem 0 2rem; }
        .cm-nav .label { font-size: .68rem; text-transform: uppercase; letter-spacing: .09em; color: #7d746d; padding: 1rem 1.25rem .35rem; }
        .cm-nav a {
            display: flex; align-items: center; gap: .65rem; padding: .55rem 1.25rem;
            color: #cfc9c4; text-decoration: none; font-size: .92rem; border-left: 3px solid transparent;
        }
        .cm-nav a:hover { background: rgba(255,255,255,.05); color: #fff; }
        .cm-nav a.active { background: rgba(181,101,29,.18); color: #fff; border-left-color: var(--cm-accent); }
        .cm-nav a i { width: 1.1rem; text-align: center; opacity: .9; }
        .cm-main { flex: 1 1 auto; min-width: 0; }
        .cm-topbar { background: #fff; border-bottom: 1px solid #e5e2df; padding: .7rem 1.5rem; display: flex; align-items: center; gap: 1rem; }
        .cm-content { padding: 1.5rem; }
        .card { border: 1px solid #e6e3e0; border-radius: .6rem; box-shadow: 0 1px 2px rgba(16,24,40,.04); }
        .card-header { background: #fff; border-bottom: 1px solid #eee9e5; font-weight: 600; }
        .btn-cafe { background: var(--cm-accent); border-color: var(--cm-accent); color: #fff; }
        .btn-cafe:hover { background: #9d571a; border-color: #9d571a; color: #fff; }
        .stat-card .value { font-size: 1.6rem; font-weight: 600; line-height: 1.1; }
        .stat-card .label { font-size: .78rem; text-transform: uppercase; letter-spacing: .06em; color: #857c74; }
        .table > :not(caption) > * > * { padding: .6rem .75rem; }
        @media (max-width: 991.98px) {
            .cm-sidebar { position: fixed; z-index: 1045; transform: translateX(-100%); transition: transform .2s; }
            .cm-sidebar.open { transform: translateX(0); }
        }
    </style>
    @stack('styles')
</head>
<body>
@php($u = auth()->user())
<div class="cm-shell">
    <aside class="cm-sidebar" id="cmSidebar">
        <div class="cm-brand">
            <h1><i class="bi bi-cup-hot-fill me-1"></i> {{ config('cafe.name') }}</h1>
            <small>Cafe management</small>
        </div>
        <nav class="cm-nav">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="{{ route('pos.index') }}" class="{{ request()->routeIs('pos.*') ? 'active' : '' }}"><i class="bi bi-basket"></i> New order (POS)</a>
            <a href="{{ route('orders.index') }}" class="{{ request()->routeIs('orders.*') ? 'active' : '' }}"><i class="bi bi-receipt"></i> Orders &amp; bills</a>
            <a href="{{ route('customers.index') }}" class="{{ request()->routeIs('customers.*') ? 'active' : '' }}"><i class="bi bi-person-vcard"></i> Customers</a>

            <div class="label">Floor</div>
            <a href="{{ route('tables.index') }}" class="{{ request()->routeIs('tables.*') ? 'active' : '' }}"><i class="bi bi-grid-3x3-gap"></i> Tables</a>
            <a href="{{ route('reservations.index') }}" class="{{ request()->routeIs('reservations.*') ? 'active' : '' }}"><i class="bi bi-calendar-check"></i> Reservations</a>

            <div class="label">Menu</div>
            <a href="{{ route('menu.index') }}" class="{{ request()->routeIs('menu.*') || request()->routeIs('recipes.*') ? 'active' : '' }}"><i class="bi bi-list-ul"></i> Menu items</a>
            <a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.*') ? 'active' : '' }}"><i class="bi bi-tags"></i> Categories</a>

            <div class="label">Inventory</div>
            <a href="{{ route('ingredients.index') }}" class="{{ request()->routeIs('ingredients.*') ? 'active' : '' }}"><i class="bi bi-box-seam"></i> Ingredients</a>
            <a href="{{ route('stock.index') }}" class="{{ request()->routeIs('stock.*') ? 'active' : '' }}"><i class="bi bi-arrow-left-right"></i> Stock movements</a>

            @if($u && $u->isAdmin())
                <div class="label">Admin</div>
                <a href="{{ route('reports.sales') }}" class="{{ request()->routeIs('reports.*') ? 'active' : '' }}"><i class="bi bi-graph-up"></i> Sales report</a>
                <a href="{{ route('staff.index') }}" class="{{ request()->routeIs('staff.*') ? 'active' : '' }}"><i class="bi bi-people"></i> Staff</a>
            @endif
        </nav>
    </aside>

    <div class="cm-main">
        <div class="cm-topbar">
            <button class="btn btn-sm btn-outline-secondary d-lg-none" onclick="document.getElementById('cmSidebar').classList.toggle('open')">
                <i class="bi bi-list"></i>
            </button>
            <div class="flex-grow-1">
                <div class="fw-semibold">@yield('title', 'Dashboard')</div>
                <div class="text-muted small">@yield('subtitle', now()->format('l, d M Y'))</div>
            </div>
            <a href="{{ route('pos.index') }}" class="btn btn-sm btn-cafe"><i class="bi bi-plus-lg"></i> New order</a>
            <div class="dropdown">
                <button class="btn btn-sm btn-light border dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle"></i> {{ $u?->name }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><span class="dropdown-item-text small text-muted">{{ $u?->email }}<br>{{ ucfirst($u?->role) }}</span></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="dropdown-item text-danger" type="submit"><i class="bi bi-box-arrow-right"></i> Log out</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>

        <div class="cm-content">
            @if(session('status'))
                <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle"></i> {{ session('status') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-triangle"></i> {{ session('error') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <div class="fw-semibold mb-1">Please fix the following:</div>
                    <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    <button class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
