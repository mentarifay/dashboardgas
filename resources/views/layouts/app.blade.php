<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Pertamina Gas Dashboard') }}</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
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
        
        /* Sidebar Styles */
        .sidebar {
            width: 280px;
            background: var(--bg-secondary);
            border-right: 1px solid var(--border-color);
            transition: transform 0.3s ease;
        }
        
        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 0.875rem 1.25rem;
            color: var(--text-secondary);
            text-decoration: none;
            transition: all 0.2s;
            border-left: 3px solid transparent;
            font-weight: 500;
        }
        
        .sidebar-link:hover {
            background: var(--bg-primary);
            color: #D71920;
            border-left-color: #D71920;
        }
        
        .sidebar-link.active {
            background: #fef2f2;
            color: #D71920;
            border-left-color: #D71920;
            font-weight: 700;
        }
        
        [data-theme="dark"] .sidebar-link.active {
            background: #7f1d1d;
            color: #fca5a5;
        }
        
        .sidebar-icon {
            width: 20px;
            margin-right: 0.875rem;
            font-size: 1.125rem;
        }
        
        .main-content {
            margin-left: 280px;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }
        
        @media (max-width: 1024px) {
            .sidebar {
                position: fixed;
                left: 0;
                top: 0;
                bottom: 0;
                z-index: 50;
                transform: translateX(-100%);
            }
            
            .sidebar.open {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .sidebar-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 40;
            }
            
            .sidebar-overlay.show {
                display: block;
            }
        }
        
        /* Dark Mode Toggle */
        .dark-mode-toggle {
            position: relative;
            width: 60px;
            height: 30px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 9999px;
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }
        
        .dark-mode-toggle-slider {
            position: absolute;
            top: 2px;
            left: 2px;
            width: 22px;
            height: 22px;
            background: white;
            border-radius: 50%;
            transition: transform 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
        }
        
        [data-theme="dark"] .dark-mode-toggle-slider {
            transform: translateX(30px);
            background: #1f2937;
            color: #fbbf24;
        }
        
        /* Role Badge */
        .role-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
        }
        
        .role-admin {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            color: white;
        }
        
        .role-user {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            color: white;
        }
        
        .role-viewer {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
            color: white;
        }
        
        /* Auto-hide alerts */
        .alert {
            animation: slideIn 0.3s ease-out;
        }
        
        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar Overlay (Mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
    
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <!-- Sidebar Header -->
        <div class="pertamina-gradient p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="bg-white rounded-lg p-2">
                        @if(file_exists(public_path('images/logo3.png')))
                            <img src="{{ asset('images/logo3.png') }}" alt="Pertamina Gas" class="h-8 w-auto">
                        @else
                            <span class="text-red-600 font-bold text-xs">PG</span>
                        @endif
                    </div>
                    <div class="ml-3">
                        <h2 class="font-bold text-sm">Pertamina Gas</h2>
                        <p class="text-xs text-red-100">Dashboard 2025</p>
                    </div>
                </div>
                <!-- Mobile Close Button -->
                <button onclick="toggleSidebar()" class="lg:hidden text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <!-- User Info -->
            <div class="bg-white bg-opacity-20 rounded-xl p-3">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-red-600 font-bold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-bold">{{ auth()->user()->name }}</p>
                        <span class="role-badge role-{{ auth()->user()->role }}">
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
        <nav class="p-4 space-y-1">
            <!-- Dashboard (All Roles) -->
            <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="sidebar-icon fas fa-home"></i>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('dashboard.comparison') }}" class="sidebar-link {{ request()->routeIs('dashboard.comparison') ? 'active' : '' }}">
                <i class="sidebar-icon fas fa-exchange-alt"></i>
                <span>Comparison</span>
            </a>

            @if(auth()->user()->canEdit())
            <!-- Divider -->
            <div class="border-t border-gray-200 my-3"></div>
            <p class="text-xs font-bold text-gray-500 px-4 mb-2 uppercase tracking-wider">Data Entry</p>

            <a href="{{ route('data.create') }}" class="sidebar-link {{ request()->routeIs('data.create') ? 'active' : '' }}">
                <i class="sidebar-icon fas fa-plus-circle"></i>
                <span>Input Data</span>
            </a>

            <a href="{{ route('data.upload') }}" class="sidebar-link {{ request()->routeIs('data.upload') ? 'active' : '' }}">
                <i class="sidebar-icon fas fa-file-upload"></i>
                <span>Upload CSV</span>
            </a>

            <a href="{{ route('my.submissions') }}" class="sidebar-link {{ request()->routeIs('my.submissions') ? 'active' : '' }}">
                <i class="sidebar-icon fas fa-list-alt"></i>
                <span>My Submissions</span>
            </a>
            @endif

            @if(auth()->user()->isAdmin())
            <!-- Divider -->
            <div class="border-t border-gray-200 my-3"></div>
            <p class="text-xs font-bold text-gray-500 px-4 mb-2 uppercase tracking-wider">Administration</p>

            <a href="{{ route('users.index') }}" class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="sidebar-icon fas fa-users"></i>
                <span>User Management</span>
            </a>

            <a href="{{ route('audit.logs') }}" class="sidebar-link {{ request()->routeIs('audit.logs') ? 'active' : '' }}">
                <i class="sidebar-icon fas fa-history"></i>
                <span>Audit Logs</span>
            </a>
            @endif

            <!-- Divider -->
            <div class="border-t border-gray-200 my-3"></div>

            <!-- Profile -->
            <a href="#" class="sidebar-link">
                <i class="sidebar-icon fas fa-user-circle"></i>
                <span>Profile</span>
            </a>

            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}" id="logoutForm">
                @csrf
                <button type="submit" class="sidebar-link w-full text-left">
                    <i class="sidebar-icon fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </button>
            </form>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Header -->
        <header class="pertamina-gradient text-white shadow-2xl sticky top-0 z-30">
            <div class="px-6 py-4">
                <div class="flex items-center justify-between">
                    <!-- Mobile Menu Button -->
                    <button onclick="toggleSidebar()" class="lg:hidden text-white">
                        <i class="fas fa-bars text-xl"></i>
                    </button>

                    <div class="flex items-center gap-4 ml-auto">
                        <!-- Dark Mode Toggle -->
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-red-100 hidden md:inline">Theme</span>
                            <div class="dark-mode-toggle" onclick="toggleDarkMode()" title="Toggle Dark Mode">
                                <div class="dark-mode-toggle-slider">
                                    <i class="fas fa-sun text-yellow-500"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Date & Time -->
                        <div class="text-right hidden md:block">
                            <p class="text-sm text-red-100 font-semibold">{{ date('d F Y') }}</p>
                            <p class="text-xs text-red-200" id="currentTime"></p>
                        </div>

                        <!-- User Dropdown -->
                        <div class="relative">
                            <button onclick="toggleUserMenu()" id="userMenuButton" class="flex items-center gap-2 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-xl px-3 py-2 transition">
                                <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center text-red-600 font-bold text-sm">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <div class="hidden md:block text-left">
                                    <p class="text-sm font-bold">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-red-100">{{ ucfirst(auth()->user()->role) }}</p>
                                </div>
                                <i class="fas fa-chevron-down text-xs" id="dropdownIcon"></i>
                            </button>

                            <!-- Dropdown Menu -->
                            <div id="userMenu" class="hidden absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-2xl border border-gray-200 py-2 z-50">
                                <!-- User Info -->
                                <div class="px-4 py-3 border-b border-gray-200">
                                    <p class="text-sm font-bold text-gray-900">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                                    <span class="inline-block mt-2 px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-bold">
                                        @if(auth()->user()->role === 'admin')
                                            <i class="fas fa-crown mr-1"></i>ADMIN
                                        @elseif(auth()->user()->role === 'user')
                                            <i class="fas fa-user mr-1"></i>USER
                                        @else
                                            <i class="fas fa-eye mr-1"></i>VIEWER
                                        @endif
                                    </span>
                                </div>

                                <!-- Menu Items -->
                                <a href="#" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50 transition text-gray-700">
                                    <i class="fas fa-user-circle text-gray-400"></i>
                                    <span class="text-sm font-medium">Profile</span>
                                </a>

                                <div class="border-t border-gray-200 my-2"></div>

                                <!-- Logout -->
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 hover:bg-red-50 transition text-red-600">
                                        <i class="fas fa-sign-out-alt"></i>
                                        <span class="text-sm font-bold">Logout</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

        <!-- Page Content -->
        <main>
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-gradient-to-r from-gray-800 to-gray-900 text-white mt-16 py-8 shadow-2xl">
            <div class="container mx-auto px-6 text-center">
                <p class="text-sm font-semibold">&copy; {{ date('Y') }} Pertamina Gas</p>
                <p class="text-xs text-gray-400 mt-2">Developed for PKL Program</p>
            </div>
        </footer>
    </div>

    <script>
        // Dark Mode
        function toggleDarkMode() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            
            const slider = document.querySelector('.dark-mode-toggle-slider i');
            if (newTheme === 'dark') {
                slider.className = 'fas fa-moon text-yellow-400';
            } else {
                slider.className = 'fas fa-sun text-yellow-500';
            }
        }
        
        // Load saved theme
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
            
            if (savedTheme === 'dark') {
                const slider = document.querySelector('.dark-mode-toggle-slider i');
                if (slider) slider.className = 'fas fa-moon text-yellow-400';
            }
        });

        // Mobile Sidebar Toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            sidebar.classList.toggle('open');
            overlay.classList.toggle('show');
        }

        // Current Time
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID', { 
                hour: '2-digit', 
                minute: '2-digit',
                second: '2-digit'
            });
            const timeElement = document.getElementById('currentTime');
            if (timeElement) {
                timeElement.textContent = timeString;
            }
        }
        
        setInterval(updateTime, 1000);
        updateTime();

        // Auto-hide alerts
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>

    @stack('scripts')
</body>
</html>