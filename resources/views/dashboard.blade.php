<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pertamina Gas - Dashboard Penyaluran</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Choices.js for Multi-Select -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* ========== CSS VARIABLES FOR DARK MODE ========== */
        :root {
            --bg-primary: #f9fafb;
            --bg-secondary: #ffffff;
            --bg-tertiary: #a4c5da;
            --text-primary: #111827;
            --text-secondary: #6b7280;
            --text-tertiary: #9ca3af;
            --border-color: #e5e7eb;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --card-bg: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
        }
        
        [data-theme="dark"] {
            --bg-primary: #111827;
            --bg-secondary: #1f2937;
            --bg-tertiary: #1e3a52;
            --text-primary: #f9fafb;
            --text-secondary: #d1d5db;
            --text-tertiary: #9ca3af;
            --border-color: #374151;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.3);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.4);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.5);
            --card-bg: linear-gradient(135deg, #1f2937 0%, #111827 100%);
        }
        
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: var(--bg-tertiary);
            color: var(--text-primary);
            transition: background 0.3s ease, color 0.3s ease;
        }
        .pertamina-gradient { 
            background: linear-gradient(135deg, #D71920 0%, #8B0000 100%); 
        }
        .card-hover { 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
        }
        .card-hover:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 20px 40px rgba(215, 25, 32, 0.15); 
        }
        .stat-card { 
            background: var(--card-bg);
            border-left: 4px solid #D71920;
        }
        
        /* Dark mode specific overrides */
        [data-theme="dark"] .stat-card { background: var(--bg-secondary); border-color: #D71920; }
        [data-theme="dark"] .bg-white { background-color: var(--bg-secondary) !important; }
        [data-theme="dark"] .text-gray-900 { color: var(--text-primary) !important; }
        [data-theme="dark"] .text-gray-700 { color: var(--text-secondary) !important; }
        [data-theme="dark"] .text-gray-600 { color: var(--text-secondary) !important; }
        [data-theme="dark"] .text-gray-500 { color: var(--text-tertiary) !important; }
        [data-theme="dark"] .border-gray-100,
        [data-theme="dark"] .border-gray-200 { border-color: var(--border-color) !important; }
        [data-theme="dark"] .bg-gray-50 { background-color: var(--bg-primary) !important; }
        [data-theme="dark"] .bg-gray-100 { background-color: #374151 !important; }
        [data-theme="dark"] .table-header-section { background: linear-gradient(to right, #1f2937, #111827) !important; }
        [data-theme="dark"] .table-subtitle { color: #e5e7eb !important; font-weight: 600 !important; }
        [data-theme="dark"] input,
        [data-theme="dark"] select,
        [data-theme="dark"] textarea { background-color: #374151 !important; color: #f9fafb !important; border-color: #4b5563 !important; }
        [data-theme="dark"] option { background-color: #374151 !important; color: #f9fafb !important; }
        [data-theme="dark"] .choices__inner { background-color: #374151 !important; border-color: #4b5563 !important; color: #f9fafb !important; }
        [data-theme="dark"] .choices__input { background-color: #374151 !important; color: #f9fafb !important; }
        [data-theme="dark"] .choices__list--dropdown { background-color: #1f2937 !important; border-color: #4b5563 !important; }
        [data-theme="dark"] .choices__list--dropdown .choices__item--selectable { background-color: #1f2937 !important; color: #f9fafb !important; }
        [data-theme="dark"] .choices__list--dropdown .choices__item--selectable.is-highlighted { background-color: #374151 !important; color: #D71920 !important; }
        [data-theme="dark"] .export-btn { background-color: #374151 !important; border-color: #4b5563 !important; color: #f9fafb !important; }
        [data-theme="dark"] .shipper-action-btn { background-color: #374151 !important; border-color: #4b5563 !important; color: #f9fafb !important; }
        [data-theme="dark"] table { color: var(--text-primary); }
        [data-theme="dark"] thead { background-color: #1f2937 !important; }
        [data-theme="dark"] tbody tr { background-color: var(--bg-secondary) !important; }
        [data-theme="dark"] tbody tr:hover { background-color: #374151 !important; }
        [data-theme="dark"] td { border-color: var(--border-color) !important; }
        [data-theme="dark"] .filter-chip { background-color: #374151; border-color: #4b5563; color: #fca5a5; }
        [data-theme="dark"] #pieStats .bg-gray-50 { background-color: #374151 !important; }

        /* ========== SIDEBAR ========== */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: 260px;
            background: var(--bg-secondary);
            border-right: 1px solid var(--border-color);
            z-index: 40;
            transition: transform 0.3s ease;
            overflow-y: auto;
        }
        .sidebar.hidden-sidebar {
            transform: translateX(-100%);
        }
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 30;
        }
        .sidebar-overlay.show { display: block; }
        .main-wrapper {
            margin-left: 260px;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }
        @media (max-width: 1024px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-wrapper { margin-left: 0; }
        }
        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.25rem;
            color: var(--text-secondary);
            text-decoration: none;
            transition: all 0.2s;
            border-left: 3px solid transparent;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            background: none;
            border-top: none;
            border-right: none;
            border-bottom: none;
            width: 100%;
            text-align: left;
        }
        .sidebar-link:hover, .sidebar-link.active {
            background: #fef2f2;
            color: #D71920;
            border-left-color: #D71920;
            font-weight: 700;
        }
        [data-theme="dark"] .sidebar-link:hover,
        [data-theme="dark"] .sidebar-link.active {
            background: #7f1d1d;
            color: #fca5a5;
        }
        .sidebar-icon { width: 18px; margin-right: 0.75rem; font-size: 1rem; }
        
        /* ========== USER DROPDOWN ========== */
        .user-dropdown { display: none; position: absolute; right: 0; top: calc(100% + 8px); width: 220px; background: white; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.15); border: 1px solid #e5e7eb; z-index: 100; }
        .user-dropdown.open { display: block; }
        [data-theme="dark"] .user-dropdown { background: #1f2937; border-color: #374151; }
        [data-theme="dark"] .user-dropdown a,
        [data-theme="dark"] .user-dropdown button { color: #d1d5db; }
        [data-theme="dark"] .user-dropdown a:hover,
        [data-theme="dark"] .user-dropdown button:hover { background: #374151; }

        /* ========== DARK MODE TOGGLE ========== */
        .dark-mode-toggle {
            position: relative; width: 56px; height: 28px;
            background: rgba(255,255,255,0.2); border-radius: 9999px;
            cursor: pointer; transition: all 0.3s;
            border: 2px solid rgba(255,255,255,0.3);
        }
        .dark-mode-toggle-slider {
            position: absolute; top: 2px; left: 2px;
            width: 20px; height: 20px; background: white;
            border-radius: 50%; transition: transform 0.3s;
            display: flex; align-items: center; justify-content: center; font-size: 10px;
        }
        [data-theme="dark"] .dark-mode-toggle-slider { transform: translateX(28px); background: #1f2937; }

        /* ========== EXISTING STYLES ========== */
        .dark-mode-toggle:hover { background: rgba(255,255,255,0.3); }
        .anomaly-pulse { animation: pulse-red 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
        @keyframes pulse-red {
            0%, 100% { opacity: 1; box-shadow: 0 0 0 0 rgba(239,68,68,0.7); }
            50% { opacity: 0.8; box-shadow: 0 0 0 10px rgba(239,68,68,0); }
        }
        .export-btn {
            padding: 0.5rem 1rem; border-radius: 0.5rem;
            font-size: 0.875rem; font-weight: 600; cursor: pointer;
            transition: all 0.2s; border: 2px solid var(--border-color);
            background: var(--bg-secondary); color: var(--text-primary);
        }
        .export-btn:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); }
        .export-btn i { margin-right: 0.5rem; }
        .chart-type-btn {
            padding: 0.5rem 1.2rem; border-radius: 0.75rem;
            border: 2px solid #e5e7eb; background: white;
            font-size: 0.85rem; font-weight: 600; cursor: pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); color: #6b7280;
        }
        .chart-type-btn:hover { border-color: #D71920; color: #D71920; transform: translateY(-2px); }
        .chart-type-btn.active { background: linear-gradient(135deg, #D71920 0%, #8B0000 100%); color: white; border-color: #D71920; }
        .trend-badge { display: inline-flex; align-items: center; padding: 0.35rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; gap: 0.25rem; }
        .trend-up { background: #dcfce7; color: #166534; }
        .trend-down { background: #fee2e2; color: #991b1b; }
        .trend-stable { background: #e0e7ff; color: #3730a3; }
        .anomaly-badge { animation: pulse-anomaly 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
        @keyframes pulse-anomaly { 0%, 100% { opacity: 1; } 50% { opacity: 0.7; } }
        .loading-spinner { border: 3px solid #f3f3f3; border-top: 3px solid #D71920; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        .filter-chip { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.4rem 0.9rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 9999px; font-size: 0.8rem; font-weight: 500; color: #991b1b; }
        .section-header { border-left: 4px solid #D71920; padding-left: 1rem; margin-bottom: 1.5rem; }
        .choices { margin-bottom: 0; }
        .choices__inner { background: white; border: 2px solid #e5e7eb; border-radius: 0.75rem; padding: 0.75rem 1rem; min-height: 48px; font-size: 0.6875rem; font-weight: 500; }
        .choices__list--multiple .choices__item { background: linear-gradient(135deg, #D71920 0%, #8B0000 100%); border: none; border-radius: 0.5rem; padding: 0.3rem 0.6rem; margin: 0.15rem; font-size: 0.65rem; font-weight: 600; }
        .choices__list--dropdown { border: 2px solid #e5e7eb; border-radius: 0.75rem; margin-top: 0.5rem; box-shadow: 0 10px 25px rgba(0,0,0,0.1); max-height: 320px; overflow-y: auto; background-color: white !important; z-index: 100; }
        .choices__list--dropdown .choices__item--selectable { padding: 0.45rem 0.75rem; font-weight: 500; font-size: 0.6875rem; background-color: white; }
        .choices__list--dropdown .choices__item--selectable.is-highlighted { background-color: #fef2f2; color: #D71920; }
        .choices__input { font-size: 0.6875rem; margin-bottom: 0; }
        .shipper-actions { display: flex; gap: 0.5rem; margin-bottom: 0.75rem; }
        .shipper-action-btn { padding: 0.4rem 0.8rem; font-size: 0.75rem; font-weight: 600; border-radius: 0.5rem; cursor: pointer; transition: all 0.2s; border: 1px solid #e5e7eb; background: white; }
        .shipper-action-btn:hover { background: #f9fafb; border-color: #D71920; color: #D71920; }
        @media (max-width: 768px) { .container { padding-left: 1rem; padding-right: 1rem; } }
    </style>
</head>
<body class="bg-gray-50">

<!-- ========== SIDEBAR OVERLAY (mobile) ========== -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<!-- ========== SIDEBAR ========== -->
<aside class="sidebar" id="sidebar">
    <!-- Sidebar Header -->
    <div class="pertamina-gradient p-5 text-white">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="bg-white rounded-lg p-2 flex items-center justify-center">
                    @if(file_exists(public_path('images/logo3.png')))
                        <img src="{{ asset('images/logo3.png') }}" alt="Pertamina Gas" class="h-7 w-auto">
                    @else
                        <span class="text-red-600 font-bold text-xs px-1">PG</span>
                    @endif
                </div>
                <div>
                    <p class="font-bold text-sm leading-tight">Pertamina Gas</p>
                    <p class="text-red-100 text-xs">Dashboard 2020-2025</p>
                </div>
            </div>
            <button onclick="closeSidebar()" class="lg:hidden text-white opacity-80 hover:opacity-100">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- User Info Card -->
        <div class="bg-white bg-opacity-20 rounded-xl p-3">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-white rounded-full flex items-center justify-center text-red-600 font-extrabold text-sm flex-shrink-0">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="overflow-hidden">
                    <p class="font-bold text-sm truncate">{{ auth()->user()->name }}</p>
                    <span class="text-xs font-bold px-2 py-0.5 rounded-full
                        @if(auth()->user()->role === 'admin') bg-red-200 text-red-900
                        @elseif(auth()->user()->role === 'user') bg-blue-200 text-blue-900
                        @else bg-gray-200 text-gray-900 @endif">
                        @if(auth()->user()->role === 'admin')
                            <i class="fas fa-crown mr-1"></i>Admin
                        @elseif(auth()->user()->role === 'user')
                            <i class="fas fa-user mr-1"></i>User
                        @else
                            <i class="fas fa-eye mr-1"></i>Viewer
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="p-3 space-y-0.5">
        <!-- Main -->
        <p class="text-xs font-bold text-gray-400 px-3 py-2 uppercase tracking-widest">Main</p>

        <a href="{{ route('dashboard') }}" class="sidebar-link active">
            <i class="sidebar-icon fas fa-home"></i> Dashboard
        </a>
        <a href="{{ route('dashboard.comparison') }}" class="sidebar-link">
            <i class="sidebar-icon fas fa-exchange-alt"></i> Comparison
        </a>

        @if(auth()->user()->canEdit())
        <!-- Data Entry -->
        <div class="border-t border-gray-100 my-2 pt-2">
            <p class="text-xs font-bold text-gray-400 px-3 py-2 uppercase tracking-widest">Data Entry</p>
        </div>
        <a href="{{ route('data.create') }}" class="sidebar-link">
            <i class="sidebar-icon fas fa-plus-circle"></i> Input Data
        </a>
        <a href="{{ route('data.upload') }}" class="sidebar-link">
            <i class="sidebar-icon fas fa-file-upload"></i> Upload CSV
        </a>
        <a href="{{ route('my.submissions') }}" class="sidebar-link">
            <i class="sidebar-icon fas fa-list-alt"></i> My Submissions
        </a>
        @endif

        @if(auth()->user()->isAdmin())
        <!-- Administration -->
        <div class="border-t border-gray-100 my-2 pt-2">
            <p class="text-xs font-bold text-gray-400 px-3 py-2 uppercase tracking-widest">Admin</p>
        </div>
        <a href="{{ route('users.index') }}" class="sidebar-link">
            <i class="sidebar-icon fas fa-users"></i> User Management
        </a>
        <a href="{{ route('audit.logs') }}" class="sidebar-link">
            <i class="sidebar-icon fas fa-history"></i> Audit Logs
        </a>
        @endif

        <!-- Account -->
        <div class="border-t border-gray-100 my-2 pt-2">
            <p class="text-xs font-bold text-gray-400 px-3 py-2 uppercase tracking-widest">Account</p>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sidebar-link">
                <i class="sidebar-icon fas fa-sign-out-alt text-red-500"></i>
                <span class="text-red-500 font-bold">Logout</span>
            </button>
        </form>
    </nav>
</aside>

<!-- ========== MAIN WRAPPER ========== -->
<div class="main-wrapper">

    <!-- ========== HEADER ========== -->
    <header class="pertamina-gradient text-white shadow-2xl sticky top-0 z-20">
        <div class="px-4 md:px-6 py-4">
            <div class="flex items-center justify-between gap-4">
                <!-- Left: Hamburger + Logo -->
                <div class="flex items-center gap-3">
                    <!-- Hamburger (mobile + toggle desktop) -->
                    <button onclick="toggleSidebar()" class="text-white opacity-80 hover:opacity-100 transition">
                        <i class="fas fa-bars text-xl"></i>
                    </button>

                    <div class="flex items-center gap-3">
                        <div class="bg-white rounded-xl p-2 flex items-center justify-center shadow-lg">
                            @if(file_exists(public_path('images/logo3.png')))
                                <img src="{{ asset('images/logo3.png') }}" alt="Pertamina Gas" class="h-8 md:h-10 w-auto object-contain">
                            @else
                                <span class="text-red-600 font-bold text-xs md:text-sm px-2">PG</span>
                            @endif
                        </div>
                        <div class="hidden sm:block">
                            <h1 class="text-lg md:text-2xl font-extrabold tracking-tight leading-tight">Pertamina Gas</h1>
                            <p class="text-red-100 text-xs font-medium">Dashboard Penyaluran Gas 2020-2025</p>
                        </div>
                    </div>
                </div>

                <!-- Right: Actions -->
                <div class="flex items-center gap-2 md:gap-4">
                    <!-- Comparison Button -->
                    <a href="{{ route('dashboard.comparison') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 md:px-4 py-2 rounded-lg transition text-xs md:text-sm font-semibold shadow-lg hidden sm:flex items-center gap-2">
                        <i class="fas fa-exchange-alt"></i>
                        <span class="hidden md:inline">Comparison</span>
                    </a>

                    <!-- Dark Mode Toggle -->
                    <div class="flex items-center gap-2">
                        <div class="dark-mode-toggle" onclick="toggleDarkMode()" title="Toggle Dark Mode">
                            <div class="dark-mode-toggle-slider">
                                <i class="fas fa-sun text-yellow-500"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Date/Time (hidden on mobile) -->
                    <div class="text-right hidden lg:block">
                        <p class="text-sm text-red-100 font-semibold">{{ date('d F Y') }}</p>
                        <p class="text-xs text-red-200" id="currentTime"></p>
                    </div>

                    <!-- User Dropdown -->
                    <div class="relative">
                        <button onclick="toggleUserMenu()" id="userMenuBtn"
                            class="flex items-center gap-2 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-xl px-3 py-2 transition">
                            <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center text-red-600 font-extrabold text-sm flex-shrink-0">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <div class="hidden md:block text-left">
                                <p class="text-sm font-bold leading-tight">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-red-100">{{ ucfirst(auth()->user()->role) }}</p>
                            </div>
                            <i class="fas fa-chevron-down text-xs" id="userMenuIcon"></i>
                        </button>

                        <!-- Dropdown Menu -->
                        <div class="user-dropdown" id="userMenuDropdown">
                            <!-- User Info -->
                            <div class="px-4 py-3 border-b border-gray-200">
                                <p class="text-sm font-bold text-gray-900">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                                <span class="inline-block mt-2 px-2 py-1 rounded-full text-xs font-bold
                                    @if(auth()->user()->role === 'admin') bg-red-100 text-red-800
                                    @elseif(auth()->user()->role === 'user') bg-blue-100 text-blue-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    @if(auth()->user()->role === 'admin')
                                        <i class="fas fa-crown mr-1"></i>ADMIN
                                    @elseif(auth()->user()->role === 'user')
                                        <i class="fas fa-user mr-1"></i>USER
                                    @else
                                        <i class="fas fa-eye mr-1"></i>VIEWER
                                    @endif
                                </span>
                            </div>

                            <!-- Menu items (role-based) -->
                            @if(auth()->user()->canEdit())
                            <a href="{{ route('data.create') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                                <i class="fas fa-plus-circle text-green-500 w-4"></i> Input Data
                            </a>
                            <a href="{{ route('my.submissions') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                                <i class="fas fa-list text-blue-500 w-4"></i> My Submissions
                            </a>
                            @endif

                            @if(auth()->user()->isAdmin())
                            <a href="{{ route('users.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                                <i class="fas fa-users text-purple-500 w-4"></i> User Management
                            </a>
                            <a href="{{ route('audit.logs') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                                <i class="fas fa-history text-orange-500 w-4"></i> Audit Logs
                            </a>
                            @endif

                            <div class="border-t border-gray-200 my-1"></div>

                            <!-- LOGOUT BUTTON -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition font-bold">
                                    <i class="fas fa-sign-out-alt w-4"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- ========== MAIN CONTENT ========== -->
    <div class="container mx-auto px-4 md:px-6 py-8">

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="stat-card rounded-2xl p-6 shadow-lg card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-xs font-bold uppercase tracking-wider mb-1">Total Volume Penyaluran</p>
                        <h3 class="text-3xl font-extrabold text-gray-900">{{ number_format($totalVolume, 2) }}</h3>
                        <p class="text-xs text-gray-500 mt-1 font-medium">MMSCFD</p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-red-100 to-red-200 rounded-2xl flex items-center justify-center shadow-md">
                        <i class="fas fa-chart-line text-red-600 text-2xl"></i>
                    </div>
                </div>
            </div>
            <div class="stat-card rounded-2xl p-6 shadow-lg card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-xs font-bold uppercase tracking-wider mb-1">Total Records</p>
                        <h3 class="text-3xl font-extrabold text-gray-900">{{ number_format($totalRecords) }}</h3>
                        <p class="text-xs text-gray-500 mt-1 font-medium">Data Points</p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-green-100 to-green-200 rounded-2xl flex items-center justify-center shadow-md">
                        <i class="fas fa-database text-green-600 text-2xl"></i>
                    </div>
                </div>
            </div>
            <div class="stat-card rounded-2xl p-6 shadow-lg card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-xs font-bold uppercase tracking-wider mb-1">Average Volume</p>
                        <h3 class="text-3xl font-extrabold text-gray-900">{{ number_format($avgVolume, 2) }}</h3>
                        <p class="text-xs text-gray-500 mt-1 font-medium">MMSCFD</p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-purple-100 to-purple-200 rounded-2xl flex items-center justify-center shadow-md">
                        <i class="fas fa-chart-bar text-purple-600 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
            <form id="filterForm" method="GET" action="{{ route('dashboard') }}" class="space-y-5">
                <div class="section-header">
                    <h2 class="text-2xl font-bold text-gray-900">
                        <i class="fas fa-filter text-red-600 mr-2"></i>Filter & Pencarian
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Filter data untuk analisis mendalam</p>
                </div>
                <div id="activeFilters" class="flex flex-wrap gap-2 mb-4"></div>
                <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                    <!-- Shipper -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-building text-red-500 mr-1"></i> Shipper
                        </label>
                        <div class="shipper-actions">
                            <button type="button" id="selectAllShippers" class="shipper-action-btn">
                                <i class="fas fa-check-double mr-1"></i> Semua
                            </button>
                            <button type="button" id="clearAllShippers" class="shipper-action-btn">
                                <i class="fas fa-times mr-1"></i> Hapus
                            </button>
                        </div>
                        <select name="shipper[]" id="filterShipper" multiple class="w-full">
                            @foreach($shippers as $shipper)
                                <option value="{{ $shipper }}" {{ is_array(request('shipper')) && in_array($shipper, request('shipper')) ? 'selected' : '' }}>
                                    {{ $shipper }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tahun Dari -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-calendar text-red-500 mr-1"></i> Tahun Dari
                        </label>
                        <select name="tahun_dari" id="filterTahunDari" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition font-medium">
                            <option value="">-- Pilih Tahun --</option>
                            @foreach($tahuns as $tahun)
                                <option value="{{ $tahun }}" {{ request('tahun_dari') == $tahun ? 'selected' : '' }}>{{ $tahun }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tahun Sampai -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-calendar text-red-500 mr-1"></i> Tahun Ke
                        </label>
                        <select name="tahun_sampai" id="filterTahunSampai" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition font-medium">
                            <option value="">-- Pilih Tahun --</option>
                            @foreach($tahuns as $tahun)
                                <option value="{{ $tahun }}" {{ request('tahun_sampai') == $tahun ? 'selected' : '' }}>{{ $tahun }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Bulan -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-calendar-day text-red-500 mr-1"></i> Bulan
                        </label>
                        <div class="shipper-actions">
                            <button type="button" id="selectAllBulan" class="shipper-action-btn">
                                <i class="fas fa-check-double mr-1"></i> Semua
                            </button>
                            <button type="button" id="clearAllBulan" class="shipper-action-btn">
                                <i class="fas fa-times mr-1"></i> Hapus
                            </button>
                        </div>
                        <select name="bulan[]" id="filterBulan" multiple class="w-full">
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ is_array(request('bulan')) && in_array($i, request('bulan')) ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-end md:col-span-2 gap-3">
                        <button type="submit" class="flex-1 pertamina-gradient text-white px-6 py-3 rounded-xl font-bold hover:opacity-90 transition shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <i class="fas fa-search mr-2"></i> Cari Data
                        </button>
                        <button type="button" onclick="resetForm()" class="px-4 py-3 bg-gray-100 text-gray-700 rounded-xl font-bold hover:bg-gray-200 transition">
                            <i class="fas fa-redo"></i>
                        </button>
                    </div>
                </div>
            </form>

            <!-- Export Buttons -->
            <div class="mt-6 pt-6 border-t border-gray-200 flex flex-wrap gap-3">
                <button onclick="exportData('excel')" class="export-btn bg-green-50 border-green-200 text-green-700 hover:bg-green-100">
                    <i class="fas fa-file-excel"></i><span class="hidden sm:inline">Export Excel</span>
                </button>
                <button onclick="exportData('csv')" class="export-btn bg-blue-50 border-blue-200 text-blue-700 hover:bg-blue-100">
                    <i class="fas fa-file-csv"></i><span class="hidden sm:inline">Export CSV</span>
                </button>
                <button onclick="exportData('pdf')" class="export-btn bg-red-50 border-red-200 text-red-700 hover:bg-red-100">
                    <i class="fas fa-file-pdf"></i><span class="hidden sm:inline">Export PDF</span>
                </button>
                <button onclick="printDashboard()" class="export-btn bg-purple-50 border-purple-200 text-purple-700 hover:bg-purple-100">
                    <i class="fas fa-print"></i><span class="hidden sm:inline">Print</span>
                </button>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Main Chart -->
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <div class="section-header border-0 p-0 m-0">
                        <h2 class="text-2xl font-bold text-gray-900">
                            <i class="fas fa-chart-area text-red-600 mr-2"></i>Trend Penyaluran Gas
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">Visualisasi data historis</p>
                    </div>
                    <div class="flex gap-2">
                        <button class="chart-type-btn active" id="btnLine" onclick="switchChart('line')">
                            <i class="fas fa-chart-line mr-1"></i> Line
                        </button>
                        <button class="chart-type-btn" id="btnBar" onclick="switchChart('bar')">
                            <i class="fas fa-chart-bar mr-1"></i> Bar
                        </button>
                    </div>
                </div>
                <div id="gasChart" style="min-height: 350px;"></div>
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Volume Cards -->
                <div class="grid grid-cols-1 gap-4">
                    <div class="bg-gradient-to-br from-emerald-500 via-green-500 to-teal-500 rounded-2xl p-6 text-white shadow-lg card-hover">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-xs font-bold uppercase tracking-wider opacity-95">Volume Tertinggi</p>
                            <i class="fas fa-arrow-trend-up text-2xl opacity-80"></i>
                        </div>
                        <h3 class="text-4xl font-extrabold mb-2">{{ number_format($volumeTertinggi->daily_average_mmscfd ?? 0, 2) }}</h3>
                        <p class="text-xs opacity-90 font-medium">MMSCFD</p>
                        <div class="mt-3 pt-3 border-t border-white border-opacity-30">
                            <p class="text-xs opacity-85"><i class="fas fa-industry mr-1"></i>{{ $volumeTertinggi->shipper ?? '-' }}</p>
                            <p class="text-xs opacity-85 mt-1">
                                <i class="fas fa-calendar mr-1"></i>
                                @if(!empty($volumeTertinggi->bulan_date))
                                    {{ date('M Y', strtotime($volumeTertinggi->bulan_date)) }}
                                @else - @endif
                            </p>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-rose-500 via-red-500 to-pink-500 rounded-2xl p-6 text-white shadow-lg card-hover">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-xs font-bold uppercase tracking-wider opacity-95">Volume Terendah</p>
                            <i class="fas fa-arrow-trend-down text-2xl opacity-80"></i>
                        </div>
                        <h3 class="text-4xl font-extrabold mb-2">{{ number_format($volumeTerendah->daily_average_mmscfd ?? 0, 2) }}</h3>
                        <p class="text-xs opacity-90 font-medium">MMSCFD</p>
                        <div class="mt-3 pt-3 border-t border-white border-opacity-30">
                            <p class="text-xs opacity-85"><i class="fas fa-industry mr-1"></i>{{ $volumeTerendah->shipper ?? '-' }}</p>
                            <p class="text-xs opacity-85 mt-1">
                                <i class="fas fa-calendar mr-1"></i>
                                @if(!empty($volumeTerendah->bulan_date))
                                    {{ date('M Y', strtotime($volumeTerendah->bulan_date)) }}
                                @else - @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Top Shipper Chart -->
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                    <div class="section-header border-0 p-0 m-0 mb-4">
                        <h2 class="text-lg font-bold text-gray-900">
                            <i class="fas fa-trophy text-amber-500 mr-2"></i>Top Shipper
                        </h2>
                        <p class="text-xs text-gray-500 mt-1">Berdasarkan filter aktif</p>
                    </div>
                    <div id="topChart" class="min-h-[220px]"></div>
                    <div id="topLoading" class="flex justify-center items-center py-12">
                        <div class="loading-spinner"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pie Chart -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
            <div class="section-header">
                <h2 class="text-2xl font-bold text-gray-900">
                    <i class="fas fa-chart-pie text-red-600 mr-2"></i>Distribusi Volume Per Shipper
                </h2>
                <p class="text-sm text-gray-500 mt-1">Persentase kontribusi setiap shipper</p>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div id="pieChart" class="min-h-[300px]"></div>
                <div id="pieLoading" class="flex justify-center items-center py-12">
                    <div class="loading-spinner"></div>
                </div>
                <div id="pieStats" class="flex flex-col justify-center space-y-3"></div>
            </div>
        </div>

        <!-- Trend Analysis -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-100" id="trendAnalysisSection">
            <div class="section-header">
                <h2 class="text-2xl font-bold text-gray-900">
                    <i class="fas fa-chart-line text-red-600 mr-2"></i>Analisis Trend Penyaluran
                </h2>
                <p class="text-sm text-gray-500 mt-1">Deteksi perubahan dan anomali volume gas</p>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-building text-red-500 mr-1"></i> Pilih Shipper untuk Analisis
                </label>
                <select id="trendShipperSelect" class="w-full md:w-1/3 px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition font-medium">
                    <option value="">-- Pilih Shipper --</option>
                    @foreach($shippers as $shipper)
                        <option value="{{ $shipper }}">{{ $shipper }}</option>
                    @endforeach
                </select>
            </div>
            <div id="trendResults" class="mt-6"></div>
            <div id="trendLoading" class="flex justify-center items-center py-12 hidden">
                <div class="loading-spinner"></div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
            <div class="table-header-section p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                <div class="section-header border-0 p-0 m-0">
                    <h2 class="text-2xl font-bold text-gray-900">
                        <i class="fas fa-table text-red-600 mr-2"></i>Data Penyaluran Gas
                    </h2>
                    <p class="table-subtitle text-sm text-gray-600 mt-1">Data berdasarkan filter yang dipilih</p>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-gray-100 to-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Shipper</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Bulan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Periode</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Daily Average (MMSCFD)</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($data as $index => $item)
                        <tr class="hover:bg-red-50 transition duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium">{{ $data->firstItem() + $index }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-bold text-gray-900">{{ $item->shipper }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-semibold">{{ date('F Y', strtotime($item->bulan_date)) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1.5 inline-flex text-xs font-bold rounded-full bg-gradient-to-r from-amber-100 to-yellow-100 text-amber-800 shadow-sm">{{ $item->periode }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="text-sm font-extrabold text-gray-900 bg-gray-100 px-3 py-1.5 rounded-lg">{{ number_format($item->daily_average_mmscfd, 2) }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                                    <p class="text-xl font-bold text-gray-500 mb-2">Tidak ada data ditemukan</p>
                                    <p class="text-sm text-gray-400">Coba ubah filter pencarian Anda</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($data->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                {{ $data->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gradient-to-r from-gray-800 to-gray-900 text-white mt-16 py-8 shadow-2xl">
        <div class="container mx-auto px-6 text-center">
            <p class="text-sm font-semibold">&copy; {{ date('Y') }} Pertamina Gas</p>
            <p class="text-xs text-gray-400 mt-2">Developed for PKL Program</p>
        </div>
    </footer>

</div><!-- end .main-wrapper -->

<script>
    let currentChartType = 'line';
    let currentChart = null;
    let currentLabels = [];
    let currentSeries = [];
    let topChart = null;
    let pieChart = null;
    let shipperChoices = null;
    let bulanChoices = null;

    // ============ SIDEBAR ============
    let sidebarOpen = true;

    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const isDesktop = window.innerWidth >= 1024;

        if (isDesktop) {
            // Desktop: toggle visibility + adjust main-wrapper margin
            sidebarOpen = !sidebarOpen;
            if (sidebarOpen) {
                sidebar.classList.remove('hidden-sidebar');
                document.querySelector('.main-wrapper').style.marginLeft = '260px';
            } else {
                sidebar.classList.add('hidden-sidebar');
                document.querySelector('.main-wrapper').style.marginLeft = '0';
            }
        } else {
            // Mobile: slide in/out with overlay
            sidebar.classList.toggle('open');
            overlay.classList.toggle('show');
        }
    }

    function closeSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        sidebar.classList.remove('open');
        overlay.classList.remove('show');
    }

    // ============ USER DROPDOWN ============
    function toggleUserMenu() {
        const dropdown = document.getElementById('userMenuDropdown');
        const icon = document.getElementById('userMenuIcon');
        dropdown.classList.toggle('open');
        icon.classList.toggle('fa-chevron-down');
        icon.classList.toggle('fa-chevron-up');
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('userMenuDropdown');
        const button = document.getElementById('userMenuBtn');
        if (button && dropdown && !button.contains(event.target) && !dropdown.contains(event.target)) {
            dropdown.classList.remove('open');
            const icon = document.getElementById('userMenuIcon');
            if (icon) { icon.classList.add('fa-chevron-down'); icon.classList.remove('fa-chevron-up'); }
        }
    });

    // ============ DARK MODE ============
    function toggleDarkMode() {
        const html = document.documentElement;
        const newTheme = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
        html.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        const slider = document.querySelector('.dark-mode-toggle-slider i');
        if (newTheme === 'dark') {
            slider.className = 'fas fa-moon text-yellow-400';
        } else {
            slider.className = 'fas fa-sun text-yellow-500';
        }
        if (currentChart) renderChart(currentChartType);
        if (topChart) loadTopChart();
        if (pieChart) loadPieChart();
    }

    // ============ REAL TIME CLOCK ============
    function updateTime() {
        const el = document.getElementById('currentTime');
        if (el) el.textContent = new Date().toLocaleTimeString('id-ID');
    }
    setInterval(updateTime, 1000);
    updateTime();

    // ============ EXPORT ============
    function exportData(format) {
        const params = getFilterParams();
        const urls = { excel: `/dashboard/export/excel`, csv: `/dashboard/export/csv`, pdf: `/dashboard/export/pdf` };
        const url = urls[format] + '?' + params;
        const btn = event.target.closest('button');
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Loading...';
        btn.disabled = true;
        if (format === 'pdf') {
            window.open(url, '_blank');
            setTimeout(() => { btn.innerHTML = originalHTML; btn.disabled = false; }, 500);
        } else {
            window.location.href = url;
            setTimeout(() => { btn.innerHTML = originalHTML; btn.disabled = false; }, 2000);
        }
    }

    function printDashboard() {
        const shipperValues = shipperChoices.getValue(true);
        const bulanValues = bulanChoices.getValue(true);
        const tahunDari = document.querySelector('[name=tahun_dari]').value;
        const tahunSampai = document.querySelector('[name=tahun_sampai]').value;
        let filterInfo = '';
        if (shipperValues.length > 0) filterInfo += `<p><strong>Shipper:</strong> ${shipperValues.join(', ')}</p>`;
        if (tahunDari || tahunSampai) filterInfo += `<p><strong>Periode:</strong> ${tahunDari || '...'} - ${tahunSampai || '...'}</p>`;
        const table = document.querySelector('table').cloneNode(true);
        const totalVolume = document.querySelectorAll('.stat-card h3')[0]?.textContent || '0';
        const totalRecords = document.querySelectorAll('.stat-card h3')[1]?.textContent || '0';
        const avgVolume = document.querySelectorAll('.stat-card h3')[2]?.textContent || '0';
        const currentDate = new Date().toLocaleString('id-ID');
        const printWindow = window.open('', 'PrintWindow', 'height=600,width=800');
        printWindow.document.open();
        printWindow.document.write(`<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Pertamina Gas Report</title>
        <style>body{font-family:Arial,sans-serif;font-size:11px;padding:20px;}.header{text-align:center;border-bottom:3px solid #D71920;padding-bottom:15px;margin-bottom:20px;}.header h1{color:#D71920;font-size:24px;}table{width:100%;border-collapse:collapse;}th{background:#D71920;color:white;padding:8px;text-align:left;}td{padding:6px 8px;border-bottom:1px solid #ddd;}tr:nth-child(even){background:#f9f9f9;}</style></head>
        <body><div class="header"><h1>PERTAMINA GAS</h1><p>Dashboard Penyaluran Gas 2020-2025</p><p>Generated: ${currentDate}</p></div>
        ${filterInfo ? `<div style="background:#f0f0f0;padding:15px;margin-bottom:20px;border-left:4px solid #D71920;">${filterInfo}</div>` : ''}
        ${table.outerHTML}
        <div style="margin-top:20px;text-align:center;font-size:9px;color:#999;">&copy; ${new Date().getFullYear()} Pertamina Gas - PKL Program</div>
        </body></html>`);
        printWindow.document.close();
        printWindow.onload = function() { printWindow.focus(); printWindow.print(); };
    }

    // ============ PIE CHART ============
    function loadPieChart() {
        const params = getFilterParams();
        document.getElementById('pieLoading').style.display = 'flex';
        document.getElementById('pieChart').style.display = 'none';
        fetch("/all-shippers-data?" + params)
            .then(res => res.json())
            .then(data => {
                document.getElementById('pieLoading').style.display = 'none';
                document.getElementById('pieChart').style.display = 'block';
                if (!data || data.length === 0) {
                    document.getElementById('pieChart').innerHTML = '<p class="text-center text-gray-400 py-8 text-sm">Tidak ada data</p>';
                    return;
                }
                if (pieChart) pieChart.destroy();
                const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
                const textColor = isDark ? '#d1d5db' : '#374151';
                const totalVolume = data.reduce((sum, item) => sum + parseFloat(item.total_volume), 0);
                const baseColors = ['#D71920','#f59e0b','#10b981','#3b82f6','#8b5cf6','#ec4899','#14b8a6','#f97316','#6366f1','#84cc16','#f43f5e','#06b6d4'];
                const colors = data.map((_, i) => baseColors[i % baseColors.length]);
                pieChart = new ApexCharts(document.querySelector("#pieChart"), {
                    series: data.map(d => parseFloat(d.total_volume)),
                    chart: { type: 'donut', height: 350, fontFamily: 'Plus Jakarta Sans, sans-serif', background: 'transparent' },
                    labels: data.map(d => d.shipper),
                    colors: colors,
                    legend: { position: 'bottom', labels: { colors: textColor } },
                    plotOptions: { pie: { donut: { size: '65%', labels: { show: true, total: { show: true, label: 'Total', color: textColor, formatter: () => totalVolume.toFixed(2) } } } } },
                    dataLabels: { enabled: true, formatter: (val) => val.toFixed(1) + '%' },
                    tooltip: { theme: isDark ? 'dark' : 'light', y: { formatter: (val) => val.toFixed(2) + ' MMSCFD (' + ((val/totalVolume)*100).toFixed(2) + '%)' } }
                });
                pieChart.render();
                let statsHTML = `<div class="mb-4 p-4 bg-gradient-to-r from-red-50 to-orange-50 rounded-lg border-l-4 border-red-600">
                    <div class="text-sm font-bold text-gray-700 mb-2">Ringkasan Distribusi</div>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div><span class="text-gray-600">Total Shipper:</span><span class="font-bold ml-1">${data.length}</span></div>
                        <div><span class="text-gray-600">Total Volume:</span><span class="font-bold ml-1">${totalVolume.toFixed(2)} MMSCFD</span></div>
                    </div></div>
                    <div class="space-y-2" style="max-height:350px;overflow-y:auto;">`;
                data.forEach((item, i) => {
                    const pct = ((parseFloat(item.total_volume)/totalVolume)*100).toFixed(1);
                    statsHTML += `<div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <div class="flex items-center gap-3"><div class="w-4 h-4 rounded" style="background:${colors[i]}"></div><span class="font-semibold text-sm">${item.shipper}</span></div>
                        <div class="text-right"><div class="font-bold text-sm">${parseFloat(item.total_volume).toFixed(2)}</div><div class="text-xs text-gray-500">${pct}%</div></div></div>`;
                });
                statsHTML += '</div>';
                document.getElementById('pieStats').innerHTML = statsHTML;
            }).catch(err => {
                console.error(err);
                document.getElementById('pieLoading').style.display = 'none';
            });
    }

    // ============ CHOICES.JS ============
    document.addEventListener('DOMContentLoaded', function() {
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);
        if (savedTheme === 'dark') {
            const slider = document.querySelector('.dark-mode-toggle-slider i');
            if (slider) slider.className = 'fas fa-moon text-yellow-400';
        }

        const shipperElement = document.getElementById('filterShipper');
        shipperChoices = new Choices(shipperElement, {
            removeItemButton: true, searchEnabled: true,
            searchPlaceholderValue: 'Cari shipper...', noResultsText: 'Tidak ditemukan',
            itemSelectText: '', maxItemCount: -1, placeholder: true,
            placeholderValue: 'Pilih shipper...', shouldSort: false
        });

        const bulanElement = document.getElementById('filterBulan');
        bulanChoices = new Choices(bulanElement, {
            removeItemButton: true, searchEnabled: false,
            itemSelectText: '', maxItemCount: -1, placeholder: true,
            placeholderValue: 'Pilih bulan...', shouldSort: false
        });

        document.getElementById('selectAllShippers').addEventListener('click', () => {
            shipperChoices.setChoiceByValue(Array.from(shipperElement.options).map(o => o.value));
        });
        document.getElementById('clearAllShippers').addEventListener('click', () => shipperChoices.removeActiveItems());
        document.getElementById('selectAllBulan').addEventListener('click', () => {
            bulanChoices.setChoiceByValue(Array.from(bulanElement.options).map(o => o.value));
        });
        document.getElementById('clearAllBulan').addEventListener('click', () => bulanChoices.removeActiveItems());

        loadMainChart();
        loadTopChart();
        loadPieChart();
        updateActiveFilters();
    });

    function resetForm() { window.location.href = "{{ route('dashboard') }}"; }

    function updateActiveFilters() {
        const filters = [];
        const shipperValues = shipperChoices ? shipperChoices.getValue(true) : [];
        const bulanValues = bulanChoices ? bulanChoices.getValue(true) : [];
        const tahunDari = document.querySelector('[name=tahun_dari]').value;
        const tahunSampai = document.querySelector('[name=tahun_sampai]').value;
        if (shipperValues.length > 0) filters.push({label: 'Shipper', value: shipperValues.length === 1 ? shipperValues[0] : shipperValues.length + ' dipilih'});
        if (bulanValues.length > 0) filters.push({label: 'Bulan', value: bulanValues.length + ' bulan'});
        if (tahunDari) filters.push({label: 'Dari', value: tahunDari});
        if (tahunSampai) filters.push({label: 'Ke', value: tahunSampai});
        const container = document.getElementById('activeFilters');
        container.innerHTML = filters.length === 0
            ? '<span class="text-sm text-gray-400 italic">Tidak ada filter aktif</span>'
            : filters.map(f => `<span class="filter-chip"><i class="fas fa-filter"></i><span>${f.label}: <strong>${f.value}</strong></span></span>`).join('');
    }

    function getFilterParams() {
        const params = new URLSearchParams();
        const shipperValues = shipperChoices ? shipperChoices.getValue(true) : [];
        const bulanValues = bulanChoices ? bulanChoices.getValue(true) : [];
        shipperValues.forEach(s => params.append('shipper[]', s));
        bulanValues.forEach(b => params.append('bulan[]', b));
        const tahunDari = document.querySelector('[name=tahun_dari]').value;
        const tahunSampai = document.querySelector('[name=tahun_sampai]').value;
        if (tahunDari) params.append('tahun_dari', tahunDari);
        if (tahunSampai) params.append('tahun_sampai', tahunSampai);
        return params.toString();
    }

    // ============ MAIN CHART ============
    function loadMainChart() {
        const params = getFilterParams();
        fetch("{{ route('chart.data') }}?" + params)
            .then(res => res.json())
            .then(data => {
                if (!data || !data.labels || data.labels.length === 0) {
                    document.getElementById('gasChart').innerHTML = '<div class="flex flex-col items-center justify-center py-16"><i class="fas fa-chart-line text-6xl text-gray-300 mb-4"></i><p class="text-gray-500 font-semibold">Tidak ada data</p></div>';
                    return;
                }
                currentLabels = data.labels;
                currentSeries = data.series;
                renderChart(currentChartType);
            }).catch(err => console.error(err));
    }

    function renderChart(type) {
        if (currentChart) { currentChart.destroy(); document.getElementById('gasChart').innerHTML = ''; }
        if (!currentSeries || currentSeries.length === 0) return;
        const colors = ['#D71920','#f59e0b','#10b981','#3b82f6','#8b5cf6','#ec4899','#14b8a6','#f97316','#6366f1','#84cc16'];
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        const textColor = isDark ? '#d1d5db' : '#374151';
        const gridColor = isDark ? '#374151' : '#f1f1f1';
        const options = {
            series: currentSeries,
            chart: { type, height: 600, toolbar: { show: true }, animations: { enabled: true, easing: 'easeinout', speed: 800 }, fontFamily: 'Plus Jakarta Sans, sans-serif', background: 'transparent' },
            colors,
            stroke: type === 'line' ? { curve: 'smooth', width: 2.5 } : { width: 0 },
            fill: { type: 'solid', opacity: type === 'line' ? 1 : 0.9 },
            markers: type === 'line' ? { size: 5, hover: { size: 6 } } : {},
            xaxis: { categories: currentLabels, labels: { style: { fontSize: '11px', fontWeight: 600, colors: textColor }, rotate: currentLabels.length > 15 ? -35 : 0 } },
            yaxis: { title: { text: 'Volume (MMSCFD)', style: { fontWeight: 700, color: textColor } }, labels: { formatter: v => v.toFixed(1), style: { colors: textColor } } },
            tooltip: { shared: true, intersect: false, theme: isDark ? 'dark' : 'light', y: { formatter: val => val.toFixed(2) + ' MMSCFD' } },
            legend: { position: 'bottom', fontSize: '12px', fontWeight: 600, labels: { colors: textColor } },
            grid: { borderColor: gridColor, strokeDashArray: 3 },
            plotOptions: type === 'bar' ? { bar: { borderRadius: 6, columnWidth: currentSeries.length > 5 ? '80%' : '60%' } } : {},
            dataLabels: { enabled: false }
        };
        currentChart = new ApexCharts(document.querySelector("#gasChart"), options);
        currentChart.render();
    }

    function switchChart(type) {
        currentChartType = type;
        document.getElementById('btnLine').classList.toggle('active', type === 'line');
        document.getElementById('btnBar').classList.toggle('active', type === 'bar');
        renderChart(type);
    }

    // ============ TOP CHART ============
    function loadTopChart() {
        const params = getFilterParams();
        document.getElementById('topLoading').style.display = 'flex';
        document.getElementById('topChart').style.display = 'none';
        fetch("{{ route('top.data') }}?" + params)
            .then(res => res.json())
            .then(data => {
                document.getElementById('topLoading').style.display = 'none';
                document.getElementById('topChart').style.display = 'block';
                if (!data || data.length === 0) {
                    document.getElementById('topChart').innerHTML = '<p class="text-center text-gray-400 py-8 text-sm">Tidak ada data</p>';
                    return;
                }
                if (topChart) topChart.destroy();
                topChart = new ApexCharts(document.querySelector("#topChart"), {
                    series: [{ name: 'Total Volume', data: data.map(d => parseFloat(d.total_volume)) }],
                    chart: { type: 'bar', height: 240, toolbar: { show: false }, fontFamily: 'Plus Jakarta Sans, sans-serif' },
                    colors: ['#D71920','#f59e0b','#10b981','#3b82f6','#8b5cf6'],
                    plotOptions: { bar: { borderRadius: 8, distributed: true, columnWidth: '60%' } },
                    xaxis: { categories: data.map(d => d.shipper), labels: { style: { fontSize: '11px', fontWeight: 600 } } },
                    yaxis: { labels: { formatter: v => v.toFixed(0) } },
                    legend: { show: false },
                    dataLabels: { enabled: true, formatter: v => v.toFixed(0), style: { fontSize: '11px', colors: ['#fff'], fontWeight: 'bold' }, offsetY: -20 },
                    tooltip: { theme: 'dark', y: { formatter: val => val.toFixed(2) + " MMSCFD" } }
                });
                topChart.render();
            }).catch(err => console.error(err));
    }

    // ============ TREND ANALYSIS ============
    document.getElementById('trendShipperSelect').addEventListener('change', function() {
        const shipper = this.value;
        if (!shipper) { document.getElementById('trendResults').innerHTML = ''; return; }
        document.getElementById('trendLoading').classList.remove('hidden');
        document.getElementById('trendResults').innerHTML = '';
        fetch("{{ route('trend.analysis') }}?shipper=" + encodeURIComponent(shipper))
            .then(res => res.json())
            .then(data => {
                document.getElementById('trendLoading').classList.add('hidden');
                if (data.error) { document.getElementById('trendResults').innerHTML = `<p class="text-red-500">${data.error}</p>`; return; }
                let html = `<div class="mb-6 p-5 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border-2 border-blue-200">
                    <h3 class="font-bold text-lg text-gray-900 mb-2"><i class="fas fa-info-circle text-blue-600 mr-2"></i>Ringkasan - ${data.shipper}</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div><span class="text-gray-600">Total Periode:</span><span class="font-bold ml-2">${data.total_periods}</span></div>
                        <div><span class="text-gray-600">Anomali:</span><span class="font-bold ${data.anomaly_count > 0 ? 'text-red-600' : 'text-green-600'} ml-2">${data.anomaly_count} periode</span></div>
                    </div></div>
                    <div class="overflow-x-auto"><table class="w-full text-sm">
                    <thead class="bg-gradient-to-r from-gray-100 to-gray-50"><tr>
                        <th class="px-4 py-3 text-left font-bold text-gray-700">Periode</th>
                        <th class="px-4 py-3 text-right font-bold text-gray-700">Volume</th>
                        <th class="px-4 py-3 text-right font-bold text-gray-700">Sebelum</th>
                        <th class="px-4 py-3 text-right font-bold text-gray-700">Selisih</th>
                        <th class="px-4 py-3 text-right font-bold text-gray-700">Perubahan</th>
                        <th class="px-4 py-3 text-center font-bold text-gray-700">Status</th>
                    </tr></thead><tbody class="divide-y divide-gray-100">`;
                data.trends.forEach(t => {
                    const badge = t.is_anomaly
                        ? `<span class="anomaly-badge px-3 py-1.5 text-xs font-bold bg-red-600 text-white rounded-full"><i class="fas fa-exclamation-triangle mr-1"></i>ANOMALI</span>`
                        : `<span class="px-3 py-1.5 text-xs font-bold bg-green-100 text-green-800 rounded-full"><i class="fas fa-check-circle mr-1"></i>NORMAL</span>`;
                    html += `<tr class="hover:bg-gray-50 transition ${t.is_anomaly ? 'bg-red-50' : ''}">
                        <td class="px-4 py-3 font-semibold">${t.periode}</td>
                        <td class="px-4 py-3 text-right font-bold">${t.volume.toLocaleString('id-ID', {minimumFractionDigits: 2})}</td>
                        <td class="px-4 py-3 text-right text-gray-600">${t.previous_volume.toLocaleString('id-ID', {minimumFractionDigits: 2})}</td>
                        <td class="px-4 py-3 text-right font-semibold ${t.change >= 0 ? 'text-green-600' : 'text-red-600'}">${t.change >= 0 ? '+' : ''}${t.change.toLocaleString('id-ID', {minimumFractionDigits: 2})}</td>
                        <td class="px-4 py-3 text-right font-bold ${t.percent_change >= 0 ? 'text-green-600' : 'text-red-600'}">${t.percent_change >= 0 ? '+' : ''}${t.percent_change.toFixed(2)}%</td>
                        <td class="px-4 py-3 text-center">${badge}</td>
                    </tr>`;
                });
                html += '</tbody></table></div>';
                document.getElementById('trendResults').innerHTML = html;
            }).catch(err => {
                document.getElementById('trendLoading').classList.add('hidden');
                document.getElementById('trendResults').innerHTML = '<p class="text-red-500">Error loading trend analysis</p>';
            });
    });

    // ============ FILTER LISTENERS ============
    document.querySelectorAll('#filterForm select:not(#filterShipper):not(#filterBulan)').forEach(sel => {
        sel.addEventListener('change', () => { loadMainChart(); loadTopChart(); loadPieChart(); updateActiveFilters(); });
    });
    document.getElementById('filterShipper').addEventListener('change', () => { loadMainChart(); loadTopChart(); loadPieChart(); updateActiveFilters(); });
    document.getElementById('filterBulan').addEventListener('change', () => { loadMainChart(); loadTopChart(); loadPieChart(); updateActiveFilters(); });
</script>
</body>
</html>