<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Restaurant ERP & POS') | Ultra Modern KDS</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Custom Modern Bootstrap Design Tokens (No Filament/Third Party Packages) -->
    <link rel="stylesheet" href="{{ asset('css/modern-bootstrap-custom.css') }}">
    @stack('styles')
</head>
<body>
    <!-- Glassmorphic Sidebar -->
    <aside class="sidebar-wrapper">
        <a href="{{ route('dashboard', [], false) ?? '#' }}" class="sidebar-brand">
            <i class="bi bi-fire me-2 text-warning"></i> ANTIGRAVITY <span class="text-white ms-1 fw-light">ERP</span>
        </a>
        
        <div class="sidebar-nav">
            <div class="text-uppercase text-secondary fw-bold fs-7 mb-2 px-3 tracking-wider">Operational Core</div>
            
            <div class="nav-item">
                <a href="{{ url('/') }}" class="nav-link-custom {{ request()->is('/') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Executive Dashboard
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ url('/pos') }}" class="nav-link-custom {{ request()->is('pos*') ? 'active' : '' }}">
                    <i class="bi bi-shop"></i> Touch POS Terminal
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ url('/kds') }}" class="nav-link-custom {{ (request()->is('kds') || request()->is('kds/index*')) ? 'active' : '' }}">
                    <i class="bi bi-display-fill text-warning"></i> Live Kitchen Display (KDS)
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ url('/kds/ai-advisor') }}" class="nav-link-custom {{ request()->is('kds/ai-advisor*') ? 'active' : '' }}">
                    <i class="bi bi-cpu-fill text-info"></i> AI Prep Advisor & Forecast
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ url('/tables') }}" class="nav-link-custom {{ (request()->is('tables') || request()->is('tables/index*')) ? 'active' : '' }}">
                    <i class="bi bi-grid-3x3-gap-fill"></i> Floor Map & Table Grid
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ url('/tables/config') }}" class="nav-link-custom {{ request()->is('tables/config*') ? 'active' : '' }}">
                    <i class="bi bi-qr-code text-warning"></i> Table Config & QRs
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ url('/delivery/dispatch') }}" class="nav-link-custom {{ request()->is('delivery*') ? 'active' : '' }}">
                    <i class="bi bi-bicycle text-info"></i> Cloud Kitchen Delivery
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ url('/hotel/banquet-and-rooms') }}" class="nav-link-custom {{ request()->is('hotel/banquet-and-rooms*') ? 'active' : '' }}">
                    <i class="bi bi-building-check text-warning"></i> In-Room Dining & Banquets
                </a>
            </div>

            <div class="text-uppercase text-secondary fw-bold fs-7 mt-4 mb-2 px-3 tracking-wider">Inventory & COGS</div>
            <div class="nav-item">
                <a href="{{ url('/menu') }}" class="nav-link-custom {{ (request()->is('menu') || request()->is('menu/index*')) ? 'active' : '' }}">
                    <i class="bi bi-book-half"></i> Menu & Recipe Mapping
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ url('/menu/profit-engineering') }}" class="nav-link-custom {{ request()->is('menu/profit-engineering*') ? 'active' : '' }}">
                    <i class="bi bi-graph-up-arrow text-warning"></i> Menu Profit Engineering
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ url('/inventory') }}" class="nav-link-custom {{ request()->is('inventory*') ? 'active' : '' }}">
                    <i class="bi bi-box-seams-fill text-success"></i> Stores & Raw Materials
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ url('/suppliers') }}" class="nav-link-custom {{ request()->is('suppliers*') ? 'active' : '' }}">
                    <i class="bi bi-truck"></i> Supplier Ledgers
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ url('/inventory/waste-and-spillage') }}" class="nav-link-custom {{ request()->is('inventory/waste*') ? 'active' : '' }}">
                    <i class="bi bi-trash3-fill text-danger"></i> Waste & Spillage Logging
                </a>
            </div>

            <div class="text-uppercase text-secondary fw-bold fs-7 mt-4 mb-2 px-3 tracking-wider">Finance & IT Control</div>
            <div class="nav-item">
                <a href="{{ url('/accounts/profit-and-loss') }}" class="nav-link-custom {{ request()->is('accounts/profit-and-loss*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-bar-graph-fill text-success"></i> Executive P&L Statement
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ url('/accounts/night-audit') }}" class="nav-link-custom {{ request()->is('accounts/night-audit*') ? 'active' : '' }}">
                    <i class="bi bi-moon-stars-fill text-danger"></i> Daily Night Audit & Close
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ url('/accounts/invoices') }}" class="nav-link-custom {{ request()->is('accounts/invoices*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-spreadsheet-fill text-info"></i> Accounting & Tax Invoices
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ url('/accounts/verify-lifecycle') }}" class="nav-link-custom {{ request()->is('accounts/verify-lifecycle*') ? 'active' : '' }}">
                    <i class="bi bi-cpu-fill text-primary"></i> Lifecycle Verification
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ url('/crm/patrons') }}" class="nav-link-custom {{ request()->is('crm/patrons*') ? 'active' : '' }}">
                    <i class="bi bi-person-hearts text-warning"></i> Patron CRM & VIP Loyalty
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ url('/users') }}" class="nav-link-custom {{ request()->is('users*') ? 'active' : '' }}">
                    <i class="bi bi-people-fill"></i> RBAC Role Permissions
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ url('/it-admin') }}" class="nav-link-custom {{ request()->is('it-admin*') ? 'active' : '' }}">
                    <i class="bi bi-shield-lock-fill text-danger"></i> IT Admin & Portals
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ url('/settings') }}" class="nav-link-custom {{ request()->is('settings*') ? 'active' : '' }}">
                    <i class="bi bi-buildings-fill text-warning"></i> Hotel & KDS Settings
                </a>
            </div>
        </div>
        
        <!-- Active User Profile Card -->
        <div class="p-3 border-top border-secondary border-opacity-25 mt-auto">
            <div class="d-flex align-items-center p-2 rounded-4" style="background: rgba(255,255,255,0.04);">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold me-3" style="width:42px; height:42px; font-size:1.1rem;">
                    {{ substr(auth()->user()->name ?? 'SA', 0, 2) }}
                </div>
                <div class="overflow-hidden">
                    <h6 class="mb-0 text-white text-truncate font-heading">{{ auth()->user()->name ?? 'Super Admin' }}</h6>
                    <span class="badge badge-emerald mt-1">{{ strtoupper(auth()->user()->role ?? 'SUPERADMIN') }}</span>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Workspace -->
    <main class="main-content">
        <!-- Top Sticky Header -->
        <header class="topbar">
            <div class="d-flex align-items-center">
                <span class="fs-4 fw-bolder text-dark dark:text-white me-3 font-heading">
                    @yield('page_title', 'Operational Overview')
                </span>
                <span class="badge badge-azure d-none d-md-inline-block">
                    <i class="bi bi-hdd-network me-1"></i> HOST: 192.168.32.249:8107
                </span>
            </div>

            <div class="d-flex align-items-center gap-3">
                <!-- Theme Mode Toggle Button -->
                <button class="btn btn-modern-outline btn-sm d-flex align-items-center" id="themeToggleBtn">
                    <i class="bi bi-moon-stars-fill me-1" id="themeIcon"></i>
                    <span id="themeText">Dark Mode</span>
                </button>

                <!-- Quick POS KOT Fire Action -->
                <a href="{{ url('/pos') }}" class="btn btn-modern-primary btn-sm d-flex align-items-center">
                    <i class="bi bi-plus-circle-fill me-1"></i> New POS Order
                </a>
                
                <!-- Quick Role Switcher (For user test demo) -->
                <div class="dropdown">
                    <button class="btn btn-dark btn-sm dropdown-toggle d-flex align-items-center rounded-3" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-badge-fill me-1 text-warning"></i> Demo Switcher
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-2" style="width: 240px;">
                        <li><h6 class="dropdown-header text-uppercase fs-7">Test Operational Roles</h6></li>
                        @foreach(['superadmin' => 'Super Admin', 'admin' => 'General Manager', 'accounts' => 'Financial Controller', 'kitchenmanager' => 'Kitchen Manager', 'chef' => 'Executive Chef (KDS)', 'itadmin' => 'IT & Network Admin', 'waiter' => 'Lead Waiter', 'cashier' => 'Main Cashier', 'storekeeper' => 'Store Keeper'] as $roleKey => $roleLabel)
                            <li><a class="dropdown-item rounded-3 d-flex align-items-center py-2" href="{{ url('/switch-role/' . $roleKey) }}">
                                <i class="bi bi-arrow-right-circle me-2 text-muted"></i> {{ $roleLabel }}
                            </a></li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </header>

        <!-- Page Body -->
        <div class="container-fluid p-4">
            @if(session('success'))
                <div class="alert alert-success card-glassmorphic border-0 text-white fw-bold py-3 mb-4 d-flex align-items-center" role="alert" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Theme Switcher & Micro-animation Controller -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const themeBtn = document.getElementById('themeToggleBtn');
            const themeIcon = document.getElementById('themeIcon');
            const themeText = document.getElementById('themeText');
            const htmlEl = document.documentElement;
            
            // Auto-load saved theme or default to light
            let currentTheme = localStorage.getItem('erp_theme') || 'light';
            setTheme(currentTheme);

            themeBtn.addEventListener('click', function() {
                currentTheme = htmlEl.getAttribute('data-bs-theme') === 'light' ? 'dark' : 'light';
                setTheme(currentTheme);
                localStorage.setItem('erp_theme', currentTheme);
            });

            function setTheme(theme) {
                htmlEl.setAttribute('data-bs-theme', theme);
                if(theme === 'dark') {
                    themeIcon.className = 'bi bi-sun-fill me-1 text-warning';
                    themeText.textContent = 'Light Mode';
                } else {
                    themeIcon.className = 'bi bi-moon-stars-fill me-1';
                    themeText.textContent = 'Dark Mode';
                }
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
