{{-- FILE: resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SwiftPOS') — {{ \App\Models\Setting::get('company_name', 'SwiftPOS Arabia') }}</title>

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    {{-- Google Font --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --sidebar-w: 240px;
            --sidebar-bg: #0f172a;
            --sidebar-border: rgba(255, 255, 255, .07);
            --accent: #3b82f6;
            --accent-hover: #2563eb;
            --topbar-h: 58px;
            --body-bg: #f1f5f9;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--body-bg);
            margin: 0;
            overflow-x: hidden;
            font-size: 13.5px;
        }

        /* ── Sidebar ── */
        #sidebar {
            width: var(--sidebar-w);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transition: width .25s;
        }

        .sidebar-logo {
            padding: 16px;
            border-bottom: 1px solid var(--sidebar-border);
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .sidebar-logo .logo-box {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            background: var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 12px;
            color: #fff;
            letter-spacing: -.5px;
            flex-shrink: 0;
        }

        .sidebar-logo .logo-name {
            color: #f8fafc;
            font-size: 15px;
            font-weight: 700;
        }

        .sidebar-logo .logo-sub {
            color: #64748b;
            font-size: 10px;
        }

        nav.sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 8px 0 20px;
        }

        nav.sidebar-nav::-webkit-scrollbar {
            width: 3px;
        }

        nav.sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, .1);
        }

        .nav-group-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #475569;
            padding: 14px 16px 4px;
            font-weight: 600;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 9px 14px;
            color: #94a3b8;
            text-decoration: none;
            font-size: 13px;
            font-weight: 400;
            border-right: 2px solid transparent;
            transition: all .15s;
            cursor: pointer;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, .05);
            color: #e2e8f0;
        }

        .nav-link.active {
            background: rgba(59, 130, 246, .12);
            color: #60a5fa;
            border-right-color: var(--accent);
        }

        .nav-link i {
            font-size: 15px;
            min-width: 18px;
            text-align: center;
        }

        .nav-link .arrow {
            margin-left: auto;
            font-size: 11px;
            transition: transform .2s;
            color: #475569;
        }

        .nav-link[aria-expanded="true"] .arrow {
            transform: rotate(90deg);
        }

        .nav-sub .nav-link {
            padding-left: 38px;
            font-size: 12px;
        }

        .nav-sub {
            background: rgba(0, 0, 0, .15);
        }

        /* ── Topbar ── */
        #topbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-w);
            right: 0;
            height: var(--topbar-h);
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            z-index: 999;
            transition: left .25s;
        }

        .topbar-title {
            font-size: 15px;
            font-weight: 600;
            color: #1e293b;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .topbar-right .btn-icon {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: none;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            cursor: pointer;
            transition: all .15s;
            text-decoration: none;
        }

        .topbar-right .btn-icon:hover {
            background: #f8fafc;
            color: #1e293b;
        }

        .user-badge {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 600;
            color: #fff;
        }

        .user-name {
            font-size: 13px;
            font-weight: 500;
            color: #1e293b;
        }

        .user-role {
            font-size: 11px;
            color: #94a3b8;
        }

        /* ── Low stock alert badge ── */
        .alert-badge {
            position: absolute;
            top: 6px;
            right: 12px;
            background: #ef4444;
            color: #fff;
            font-size: 9px;
            font-weight: 700;
            padding: 1px 5px;
            border-radius: 10px;
            min-width: 16px;
            text-align: center;
        }

        /* ── Main content ── */
        #main {
            margin-left: var(--sidebar-w);
            padding-top: var(--topbar-h);
            min-height: 100vh;
            transition: margin-left .25s;
        }

        .content-wrap {
            padding: 22px;
        }

        /* ── Cards ── */
        .card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: none;
        }

        .card-header {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            border-radius: 12px 12px 0 0 !important;
            padding: 14px 18px;
            font-weight: 600;
            font-size: 14px;
            color: #1e293b;
        }

        .stat-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 18px;
        }

        .stat-card .stat-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #94a3b8;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .stat-card .stat-value {
            font-size: 24px;
            font-weight: 700;
        }

        .stat-card .stat-change {
            font-size: 11px;
            margin-top: 4px;
        }

        /* ── Tables ── */
        .table {
            font-size: 13px;
        }

        .table thead th {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #94a3b8;
            font-weight: 600;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }

        .table tbody tr:hover td {
            background: #f8fafc;
        }

        /* ── Badges ── */
        .badge-paid {
            background: #dcfce7;
            color: #16a34a;
        }

        .badge-unpaid {
            background: #fee2e2;
            color: #dc2626;
        }

        .badge-partial {
            background: #fef3c7;
            color: #d97706;
        }

        .badge-draft {
            background: #f1f5f9;
            color: #64748b;
        }

        .badge-cancelled {
            background: #fce7f3;
            color: #9d174d;
        }

        .badge-converted {
            background: #ede9fe;
            color: #7c3aed;
        }

        .status-badge {
            font-size: 11px;
            font-weight: 600;
            padding: 3px 9px;
            border-radius: 20px;
        }

        /* ── Forms ── */
        .form-control,
        .form-select {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 13px;
            padding: 8px 12px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, .1);
        }

        .form-label {
            font-size: 12px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        /* ── Buttons ── */
        .btn-primary {
            background: var(--accent);
            border-color: var(--accent);
        }

        .btn-primary:hover {
            background: var(--accent-hover);
            border-color: var(--accent-hover);
        }

        .btn-sm {
            font-size: 12px;
            padding: 5px 12px;
        }

        /* ── Alerts ── */
        .alert {
            border-radius: 10px;
            font-size: 13px;
            border: none;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
        }

        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        /* ── Low stock row ── */
        .low-stock-row td {
            color: #dc2626 !important;
        }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            #sidebar {
                width: 0;
            }

            #topbar,
            #main {
                left: 0;
                margin-left: 0;
            }

            #sidebar.open {
                width: var(--sidebar-w);
            }
        }
    </style>

    @stack('styles')
</head>

<body>

    {{-- ══ SIDEBAR ══ --}}
    <div id="sidebar">
        <a class="sidebar-logo" href="{{ route('dashboard') }}">
            <div class="logo-box">POS</div>
            <div>
                <div class="logo-name">SwiftPOS</div>
                <div class="logo-sub">Arabia Edition</div>
            </div>
        </a>

        <nav class="sidebar-nav">
            {{-- Main --}}
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="{{ route('pos.index') }}" class="nav-link {{ request()->routeIs('pos.*') ? 'active' : '' }}">
                <i class="bi bi-bag-check"></i> POS
            </a>
            <a href="{{ route('client.index') }}" class="nav-link {{ request()->routeIs('client.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Client
            </a>
            <a href="{{ route('invoices.index') }}" class="nav-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                <i class="bi bi-receipt"></i> Invoice
            </a>
            <a href="{{ route('quotations.index') }}" class="nav-link {{ request()->routeIs('quotations.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text"></i> Quotation
            </a>
            <a href="{{ route('product.index') }}" class="nav-link {{ request()->routeIs('product.*') ? 'active' : '' }}" style="position:relative">
                <i class="bi bi-box-seam"></i> Product
                @php $lowStock = \App\Models\Product::lowStock()->count(); @endphp
                @if($lowStock > 0)
                <span class="alert-badge">{{ $lowStock }}</span>
                @endif
            </a>
            <a href="{{ route('barcodes.index') }}" class="nav-link {{ request()->routeIs('barcodes.*') ? 'active' : '' }}">
                <i class="bi bi-upc-scan"></i> Barcode
            </a>

            {{-- Account --}}
            <div class="nav-group-label">Account</div>
            <a class="nav-link {{ request()->routeIs('income.*') || request()->routeIs('expense.*') ? '' : 'collapsed' }}"
                data-bs-toggle="collapse" href="#account-sub" role="button"
                aria-expanded="{{ request()->routeIs('income.*') || request()->routeIs('expense.*') ? 'true' : 'false' }}">
                <i class="bi bi-wallet2"></i> Account <span class="arrow"><i class="bi bi-chevron-right"></i></span>
            </a>
            <div class="collapse nav-sub {{ request()->routeIs('income.*') || request()->routeIs('expense.*') ? 'show' : '' }}" id="account-sub">
                <a href="{{ route('income.index') }}" class="nav-link {{ request()->routeIs('income.*') ? 'active' : '' }}"><i class="bi bi-arrow-up-circle"></i> Income</a>
                <a href="{{ route('expense.index') }}" class="nav-link {{ request()->routeIs('expense.*') ? 'active' : '' }}"><i class="bi bi-arrow-down-circle"></i> Expense</a>
            </div>

            {{-- Reports --}}
            <div class="nav-group-label">Reports</div>
            <a class="nav-link {{ request()->routeIs('reports.*') ? '' : 'collapsed' }}"
                data-bs-toggle="collapse" href="#report-sub" role="button"
                aria-expanded="{{ request()->routeIs('reports.*') ? 'true' : 'false' }}">
                <i class="bi bi-bar-chart-line"></i> Report <span class="arrow"><i class="bi bi-chevron-right"></i></span>
            </a>
            <div class="collapse nav-sub {{ request()->routeIs('reports.*') ? 'show' : '' }}" id="report-sub">
                <a href="{{ route('reports.invoices') }}" class="nav-link {{ request()->routeIs('reports.invoices') ? 'active' : '' }}"><i class="bi bi-file-bar-graph"></i> Invoice Overview</a>
            </div>

            {{-- Admin --}}
            @if(auth()->user()->isSuperAdmin())
            <div class="nav-group-label">Administrator</div>
            <a class="nav-link {{ request()->routeIs('roles.*') || request()->routeIs('units.*') ? '' : 'collapsed' }}"
                data-bs-toggle="collapse" href="#admin-sub" role="button"
                aria-expanded="{{ request()->routeIs('roles.*') || request()->routeIs('units.*') ? 'true' : 'false' }}">
                <i class="bi bi-shield-lock"></i> Administrator <span class="arrow"><i class="bi bi-chevron-right"></i></span>
            </a>
            <div class="collapse nav-sub {{ request()->routeIs('roles.*') || request()->routeIs('units.*') ? 'show' : '' }}" id="admin-sub">
                <a href="{{ route('roles.index') }}" class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}"><i class="bi bi-person-badge"></i> Role</a>
                <a href="{{ route('units.index') }}" class="nav-link {{ request()->routeIs('units.*') ? 'active' : '' }}"><i class="bi bi-rulers"></i> Unit</a>
            </div>
            <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <i class="bi bi-gear"></i> Setting
            </a>
            <a href="{{ route('backup.index') }}" class="nav-link {{ request()->routeIs('backup.*') ? 'active' : '' }}">
                <i class="bi bi-cloud-arrow-down"></i> Backup Database
            </a>
            @endif
        </nav>

        <div style="padding: 12px 14px; border-top: 1px solid var(--sidebar-border);">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="nav-link w-100 border-0" style="background:none; text-align:left;">
                    <i class="bi bi-box-arrow-left"></i> Logout
                </button>
            </form>
        </div>
    </div>

    {{-- ══ TOPBAR ══ --}}
    <div id="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn-icon d-md-none" id="sidebar-toggle"><i class="bi bi-list" style="font-size:18px"></i></button>
            <span class="topbar-title">@yield('page-title', 'Dashboard')</span>
        </div>
        <div class="topbar-right">
            <span style="font-size:12px; color:#94a3b8">{{ now()->format('D, d M Y') }}</span>
            <a href="{{ route('pos.index') }}" class="btn-icon" title="Quick POS"><i class="bi bi-bag-plus"></i></a>
            <div class="user-badge">
                <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
                <div>
                    <div class="user-name">{{ auth()->user()->name }}</div>
                    <div class="user-role">{{ auth()->user()->role?->name }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ MAIN ══ --}}
    <div id="main">
        <div class="content-wrap">
            {{-- Flash messages --}}
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif
            @if(session('error') || $errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                {{ session('error') ?? $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @yield('content')
        </div>
    </div>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Mobile sidebar toggle
        document.getElementById('sidebar-toggle')?.addEventListener('click', () => {
            document.getElementById('sidebar').classList.toggle('open');
        });

        // Auto-dismiss alerts
        setTimeout(() => {
            document.querySelectorAll('.alert.fade.show').forEach(el => {
                bootstrap.Alert.getOrCreateInstance(el).close();
            });
        }, 4000);
    </script>

    @stack('scripts')
</body>

</html>