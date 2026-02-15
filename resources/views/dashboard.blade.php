<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        [data-theme="dark"] .stat-card {
            background: var(--bg-secondary);
            border-color: #D71920;
        }
        [data-theme="dark"] .bg-white {
            background-color: var(--bg-secondary) !important;
        }
        [data-theme="dark"] .text-gray-900 {
            color: var(--text-primary) !important;
        }
        [data-theme="dark"] .text-gray-700 {
            color: var(--text-secondary) !important;
        }
        [data-theme="dark"] .text-gray-600 {
            color: var(--text-secondary) !important;
        }
        [data-theme="dark"] .text-gray-500 {
            color: var(--text-tertiary) !important;
        }
        [data-theme="dark"] .border-gray-100,
        [data-theme="dark"] .border-gray-200 {
            border-color: var(--border-color) !important;
        }
        [data-theme="dark"] .bg-gray-50 {
            background-color: var(--bg-primary) !important;
        }
        [data-theme="dark"] .bg-gray-100 {
            background-color: #374151 !important;
        }
        
        /* PERBAIKAN UTAMA: Background header tabel di dark mode */
        [data-theme="dark"] .table-header-section {
            background: linear-gradient(to right, #1f2937, #111827) !important;
        }
        
        /* PERBAIKAN: Text subtitle lebih terang di dark mode */
        [data-theme="dark"] .table-subtitle {
            color: #e5e7eb !important;
            font-weight: 600 !important;
        }
        
        /* Fix for form inputs and selects in dark mode */
        [data-theme="dark"] input,
        [data-theme="dark"] select,
        [data-theme="dark"] textarea {
            background-color: #374151 !important;
            color: #f9fafb !important;
            border-color: #4b5563 !important;
        }
        [data-theme="dark"] input::placeholder,
        [data-theme="dark"] select::placeholder,
        [data-theme="dark"] textarea::placeholder {
            color: #9ca3af !important;
        }
        [data-theme="dark"] option {
            background-color: #374151 !important;
            color: #f9fafb !important;
        }
        
        /* Fix Choices.js in dark mode */
        [data-theme="dark"] .choices__inner {
            background-color: #374151 !important;
            border-color: #4b5563 !important;
            color: #f9fafb !important;
        }
        [data-theme="dark"] .choices__input {
            background-color: #374151 !important;
            color: #f9fafb !important;
        }
        [data-theme="dark"] .choices__list--dropdown {
            background-color: #1f2937 !important;
            border-color: #4b5563 !important;
        }
        [data-theme="dark"] .choices__list--dropdown .choices__item--selectable {
            background-color: #1f2937 !important;
            color: #f9fafb !important;
        }
        [data-theme="dark"] .choices__list--dropdown .choices__item--selectable.is-highlighted {
            background-color: #374151 !important;
            color: #D71920 !important;
        }
        [data-theme="dark"] .choices__placeholder {
            color: #9ca3af !important;
        }
        
        /* Fix export buttons in dark mode */
        [data-theme="dark"] .export-btn {
            background-color: #374151 !important;
            border-color: #4b5563 !important;
            color: #f9fafb !important;
        }
        [data-theme="dark"] .export-btn:hover {
            background-color: #4b5563 !important;
        }
        [data-theme="dark"] .export-btn.bg-green-50 {
            background-color: #065f46 !important;
            border-color: #059669 !important;
            color: #d1fae5 !important;
        }
        [data-theme="dark"] .export-btn.bg-blue-50 {
            background-color: #1e3a8a !important;
            border-color: #2563eb !important;
            color: #dbeafe !important;
        }
        [data-theme="dark"] .export-btn.bg-red-50 {
            background-color: #7f1d1d !important;
            border-color: #dc2626 !important;
            color: #fee2e2 !important;
        }
        [data-theme="dark"] .export-btn.bg-purple-50 {
            background-color: #581c87 !important;
            border-color: #9333ea !important;
            color: #f3e8ff !important;
        }
        
        /* Fix shipper action buttons in dark mode */
        [data-theme="dark"] .shipper-action-btn {
            background-color: #374151 !important;
            border-color: #4b5563 !important;
            color: #f9fafb !important;
        }
        [data-theme="dark"] .shipper-action-btn:hover {
            background-color: #4b5563 !important;
            border-color: #D71920 !important;
            color: #fca5a5 !important;
        }
        
        /* Fix logo container in dark mode */
        [data-theme="dark"] .logo-container {
            background-color: #ffffff !important;
            /* Keep logo background white for visibility */
        }
        
        /* Fix table in dark mode */
        [data-theme="dark"] table {
            color: var(--text-primary);
        }
        [data-theme="dark"] thead {
            background-color: #1f2937 !important;
        }
        [data-theme="dark"] tbody tr {
            background-color: var(--bg-secondary) !important;
        }
        [data-theme="dark"] tbody tr:hover {
            background-color: #374151 !important;
        }
        [data-theme="dark"] td {
            border-color: var(--border-color) !important;
        }
        
        /* Fix filter chips in dark mode */
        [data-theme="dark"] .filter-chip {
            background-color: #374151;
            border-color: #4b5563;
            color: #fca5a5;
        }
        
        /* Fix active filter display */
        [data-theme="dark"] #activeFilters {
            color: var(--text-primary);
        }
        
        /* Fix pie stats in dark mode */
        [data-theme="dark"] #pieStats .bg-gray-50 {
            background-color: #374151 !important;
        }
        [data-theme="dark"] #pieStats .bg-gradient-to-r {
            background: linear-gradient(to right, #374151, #4b5563) !important;
        }
        [data-theme="dark"] #pieStats .text-gray-700,
        [data-theme="dark"] #pieStats .text-gray-900 {
            color: #f9fafb !important;
        }
        [data-theme="dark"] #pieStats .text-gray-600 {
            color: #d1d5db !important;
        }
        [data-theme="dark"] #pieStats .text-gray-500 {
            color: #9ca3af !important;
        }
        [data-theme="dark"] #pieStats .border-red-600 {
            border-color: #dc2626 !important;
        }
        [data-theme="dark"] #pieStats::-webkit-scrollbar {
            width: 8px;
        }
        [data-theme="dark"] #pieStats::-webkit-scrollbar-track {
            background: #1f2937;
            border-radius: 4px;
        }
        [data-theme="dark"] #pieStats::-webkit-scrollbar-thumb {
            background: #4b5563;
            border-radius: 4px;
        }
        [data-theme="dark"] #pieStats::-webkit-scrollbar-thumb:hover {
            background: #6b7280;
        }
        
        /* Fix volume cards gradient backgrounds in dark mode */
        [data-theme="dark"] .bg-gradient-to-br {
            opacity: 0.9;
        }
        
        /* Fix section headers in dark mode */
        [data-theme="dark"] .section-header {
            border-color: #D71920;
        }
        
        /* Ensure reset button visibility in dark mode */
        [data-theme="dark"] button[onclick="resetForm()"] {
            background-color: #374151 !important;
            color: #f9fafb !important;
        }
        [data-theme="dark"] button[onclick="resetForm()"]:hover {
            background-color: #4b5563 !important;
        }
        
        /* Dark Mode Toggle Button */
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
        .dark-mode-toggle:hover {
            background: rgba(255, 255, 255, 0.3);
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
        
        /* Anomaly Pulse Badge */
        .anomaly-pulse {
            animation: pulse-red 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes pulse-red {
            0%, 100% {
                opacity: 1;
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
            }
            50% {
                opacity: 0.8;
                box-shadow: 0 0 0 10px rgba(239, 68, 68, 0);
            }
        }
        .anomaly-indicator {
            position: absolute;
            top: -8px;
            right: -8px;
            width: 24px;
            height: 24px;
            background: #ef4444;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
            animation: pulse-red 2s infinite;
        }
        
        /* Export Buttons */
        .export-btn {
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            border: 2px solid var(--border-color);
            background: var(--bg-secondary);
            color: var(--text-primary);
        }
        .export-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
        .export-btn i {
            margin-right: 0.5rem;
        }
        
        /* Mobile Responsive Optimizations */
        @media (max-width: 768px) {
            .container {
                padding-left: 1rem;
                padding-right: 1rem;
            }
            .stat-card h3 {
                font-size: 1.5rem;
            }
            .chart-type-btn {
                padding: 0.4rem 0.8rem;
                font-size: 0.75rem;
            }
            .shipper-actions {
                flex-direction: column;
            }
            .shipper-action-btn {
                width: 100%;
            }
        }
        @media (max-width: 640px) {
            h1 {
                font-size: 1.5rem !important;
            }
            .section-header h2 {
                font-size: 1.25rem;
            }
        }

        .chart-type-btn {
            padding: 0.5rem 1.2rem;
            border-radius: 0.75rem;
            border: 2px solid #e5e7eb;
            background: white;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            color: #6b7280;
        }
        .chart-type-btn:hover { 
            border-color: #D71920; 
            color: #D71920; 
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(215, 25, 32, 0.15);
        }
        .chart-type-btn.active { 
            background: linear-gradient(135deg, #D71920 0%, #8B0000 100%); 
            color: white; 
            border-color: #D71920;
            box-shadow: 0 4px 12px rgba(215, 25, 32, 0.3);
        }

        .trend-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.35rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            gap: 0.25rem;
        }
        .trend-up { background: #dcfce7; color: #166534; }
        .trend-down { background: #fee2e2; color: #991b1b; }
        .trend-stable { background: #e0e7ff; color: #3730a3; }
        
        .anomaly-badge {
            animation: pulse-anomaly 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        @keyframes pulse-anomaly {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        .loading-spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #D71920;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .filter-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 0.9rem;
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 500;
            color: #991b1b;
        }

        .section-header {
            border-left: 4px solid #D71920;
            padding-left: 1rem;
            margin-bottom: 1.5rem;
        }
        
        /* Custom Choices.js Style - FONT LEBIH KECIL LAGI */
        .choices {
            margin-bottom: 0;
        }
        .choices__inner {
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            min-height: 48px;
            font-size: 0.6875rem;
            font-weight: 500;
        }
        .choices__inner:focus,
        .choices.is-focused .choices__inner {
            border-color: #D71920;
            box-shadow: 0 0 0 3px rgba(215, 25, 32, 0.1);
        }
        .choices__list--multiple .choices__item {
            background: linear-gradient(135deg, #D71920 0%, #8B0000 100%);
            border: none;
            border-radius: 0.5rem;
            padding: 0.3rem 0.6rem;
            margin: 0.15rem;
            font-size: 0.65rem;
            font-weight: 600;
        }
        .choices__list--dropdown {
            border: 2px solid #e5e7eb;
            border-radius: 0.75rem;
            margin-top: 0.5rem;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            max-height: 320px;
            overflow-y: auto;
            background-color: white !important;
            z-index: 100;
        }
        .choices__list--dropdown .choices__item--selectable {
            padding: 0.45rem 0.75rem;
            font-weight: 500;
            font-size: 0.6875rem;
            line-height: 1.3;
            background-color: white;
        }
        .choices__list--dropdown .choices__item--selectable.is-highlighted {
            background-color: #fef2f2;
            color: #D71920;
        }
        .choices__input {
            font-size: 0.6875rem;
            margin-bottom: 0;
        }
        .choices__button {
            border-left: 1px solid rgba(255,255,255,0.3);
            opacity: 1;
            padding: 0 8px;
        }
        .choices__list--dropdown .choices__item {
            font-size: 0.6875rem;
        }
        
        /* Fix text overflow */
        .choices__item {
            white-space: nowrap;
            overflow: visible;
        }
        
        /* Hide "Press to select" text */
        .choices__item[data-select-text] {
            font-size: 0 !important;
        }
        .choices__item[data-select-text]::after {
            content: '';
            font-size: 0;
        }
        
        /* Custom Select All / Clear All Buttons */
        .shipper-actions {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 0.75rem;
        }
        .shipper-action-btn {
            padding: 0.4rem 0.8rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid #e5e7eb;
            background: white;
        }
        .shipper-action-btn:hover {
            background: #f9fafb;
            border-color: #D71920;
            color: #D71920;
        }
    </style>
</head>
<body class="bg-gray-50">

    <!-- Header -->
    <header class="pertamina-gradient text-white shadow-2xl">
        <div class="container mx-auto px-4 md:px-6 py-5">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center space-x-3 md:space-x-4">
                    <div class="logo-container bg-white rounded-xl p-2.5 flex items-center justify-center shadow-lg">
                        @if(file_exists(public_path('images/logo3.png')))
                            <img src="{{ asset('images/logo3.png') }}" alt="Pertamina Gas" class="h-8 md:h-11 w-auto object-contain">
                        @else
                            <span class="text-red-600 font-bold text-xs md:text-sm px-2">PERTAMINA GAS</span>
                        @endif
                    </div>
                    <div>
                        <h1 class="text-xl md:text-3xl font-extrabold tracking-tight">Pertamina Gas</h1>
                        <p class="text-red-100 text-xs md:text-sm font-medium">Dashboard Penyaluran Gas 2020-2025</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <!-- Comparison Button -->
                     <a href= "{{ route('dashboard.comparison') }}" class ="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition text-sm font-semibold shadow-lg">
                        <i class="fas fa-exchange-alt mr-2"></i>
                        <span class="hidden md:inline">Comparison Analysis</span>
                        <span class="md:hidden">Compare</span>
                    </a>
                    <!-- Dark Mode Toggle -->
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-red-100 hidden md:inline">Theme</span>
                        <div class="dark-mode-toggle" onclick="toggleDarkMode()" title="Toggle Dark Mode">
                            <div class="dark-mode-toggle-slider">
                                <i class="fas fa-sun text-yellow-500"></i>
                            </div>
                        </div>
                    </div>
                    <div class="text-right hidden md:block">
                        <p class="text-sm text-red-100 font-semibold">{{ date('d F Y') }}</p>
                        <p class="text-xs text-red-200">Real-time Monitoring</p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container mx-auto px-6 py-8">

        <!-- KPI Cards (3) - TIDAK TERFILTER -->
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
                        <i class="fas fa-filter text-red-600 mr-2"></i>
                        Filter & Pencarian
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Filter data untuk analisis mendalam</p>
                </div>

                <!-- Active Filters Display -->
                <div id="activeFilters" class="flex flex-wrap gap-2 mb-4"></div>

                <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                    <!-- Shipper Multi-Select -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-building text-red-500 mr-1"></i> Shipper
                        </label>
                        
                        <!-- Select All / Clear All Buttons -->
                        <div class="shipper-actions">
                            <button type="button" id="selectAllShippers" class="shipper-action-btn">
                                <i class="fas fa-check-double mr-1"></i> Pilih Semua
                            </button>
                            <button type="button" id="clearAllShippers" class="shipper-action-btn">
                                <i class="fas fa-times mr-1"></i> Hapus Semua
                            </button>
                        </div>
                        
                        <select name="shipper[]" id="filterShipper" multiple class="w-full">
                            @foreach($shippers as $shipper)
                                <option value="{{ $shipper }}" 
                                    {{ is_array(request('shipper')) && in_array($shipper, request('shipper')) ? 'selected' : '' }}>
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

                    <!-- Bulan Multi-Select -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-calendar-day text-red-500 mr-1"></i> Bulan
                        </label>
                        
                        <!-- Select All / Clear All Buttons for Bulan -->
                        <div class="shipper-actions">
                            <button type="button" id="selectAllBulan" class="shipper-action-btn">
                                <i class="fas fa-check-double mr-1"></i> Pilih Semua
                            </button>
                            <button type="button" id="clearAllBulan" class="shipper-action-btn">
                                <i class="fas fa-times mr-1"></i> Hapus Semua
                            </button>
                        </div>
                        
                        <select name="bulan[]" id="filterBulan" multiple class="w-full">
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" 
                                    {{ is_array(request('bulan')) && in_array($i, request('bulan')) ? 'selected' : '' }}>
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
                    <i class="fas fa-file-excel"></i>
                    <span class="hidden sm:inline">Export Excel</span>
                </button>
                <button onclick="exportData('csv')" class="export-btn bg-blue-50 border-blue-200 text-blue-700 hover:bg-blue-100">
                    <i class="fas fa-file-csv"></i>
                    <span class="hidden sm:inline">Export CSV</span>
                </button>
                <button onclick="exportData('pdf')" class="export-btn bg-red-50 border-red-200 text-red-700 hover:bg-red-100">
                    <i class="fas fa-file-pdf"></i>
                    <span class="hidden sm:inline">Export PDF</span>
                </button>
                <button onclick="printDashboard()" class="export-btn bg-purple-50 border-purple-200 text-purple-700 hover:bg-purple-100">
                    <i class="fas fa-print"></i>
                    <span class="hidden sm:inline">Print</span>
                </button>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">

            <!-- Main Chart (2 kolom) -->
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <div class="section-header border-0 p-0 m-0">
                        <h2 class="text-2xl font-bold text-gray-900">
                            <i class="fas fa-chart-area text-red-600 mr-2"></i>
                            Trend Penyaluran Gas
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">Visualisasi data historis</p>
                    </div>
                    <!-- Toggle Line / Bar -->
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
                <!-- Volume Tertinggi & Terendah - FILTERED -->
                <div class="grid grid-cols-1 gap-4">
                    <div class="bg-gradient-to-br from-emerald-500 via-green-500 to-teal-500 rounded-2xl p-6 text-white shadow-lg card-hover">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-xs font-bold uppercase tracking-wider opacity-95">Volume Tertinggi</p>
                            <i class="fas fa-arrow-trend-up text-2xl opacity-80"></i>
                        </div>
                        <h3 class="text-4xl font-extrabold mb-2">{{ number_format($volumeTertinggi->daily_average_mmscfd ?? 0, 2) }}</h3>
                        <p class="text-xs opacity-90 font-medium">MMSCFD</p>
                        <div class="mt-3 pt-3 border-t border-white border-opacity-30">
                            <p class="text-xs opacity-85">
                                <i class="fas fa-industry mr-1"></i>{{ $volumeTertinggi->shipper ?? '-' }}
                            </p>
                            <p class="text-xs opacity-85 mt-1">
                                <i class="fas fa-calendar mr-1"></i>{{ !empty($volumeTertinggi->bulan) ? date('M Y', strtotime($volumeTertinggi->bulan)) : '-' }}
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
                            <p class="text-xs opacity-85">
                                <i class="fas fa-industry mr-1"></i>{{ $volumeTerendah->shipper ?? '-' }}
                            </p>
                            <p class="text-xs opacity-85 mt-1">
                                <i class="fas fa-calendar mr-1"></i>{{ !empty($volumeTerendah->bulan) ? date('M Y', strtotime($volumeTerendah->bulan)) : '-' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Top 5 Shipper - FILTERED -->
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                    <div class="section-header border-0 p-0 m-0 mb-4">
                        <h2 class="text-lg font-bold text-gray-900">
                            <i class="fas fa-trophy text-amber-500 mr-2"></i>
                            Top Shipper
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

        <!-- Pie Chart Section (NEW!) -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
            <div class="section-header">
                <h2 class="text-2xl font-bold text-gray-900">
                    <i class="fas fa-chart-pie text-red-600 mr-2"></i>
                    Distribusi Volume Per Shipper
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

        <!-- Trend Analysis Section -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-100" id="trendAnalysisSection" style="display: none;">
            <div class="section-header">
                <h2 class="text-2xl font-bold text-gray-900">
                    <i class="fas fa-chart-line text-red-600 mr-2"></i>
                    Analisis Trend Penyaluran
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
            <div id="trendLoading" class="flex justify-center items-center py-12" style="display: none;">
                <div class="loading-spinner"></div>
            </div>
        </div>

        <!-- Data Table - FILTERED -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
            <div class="table-header-section p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                <div class="section-header border-0 p-0 m-0">
                    <h2 class="text-2xl font-bold text-gray-900">
                        <i class="fas fa-table text-red-600 mr-2"></i>
                        Data Penyaluran Gas
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
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-semibold">{{ date('F Y', strtotime($item->bulan)) }}</td>
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
            <p class="text-sm font-semibold">&copy; 2025 Pertamina Gas</p>
            <p class="text-xs text-gray-400 mt-2">Developed for PKL Program</p>
        </div>
    </footer>

    <script>
        let currentChartType = 'line';
        let currentChart = null;
        let currentLabels = [];
        let currentSeries = [];
        let topChart = null;
        let pieChart = null;
        let shipperChoices = null;
        let bulanChoices = null;

        // ============ DARK MODE ============
        function toggleDarkMode() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            
            // Update icon
            const slider = document.querySelector('.dark-mode-toggle-slider i');
            if (newTheme === 'dark') {
                slider.className = 'fas fa-moon text-yellow-400';
            } else {
                slider.className = 'fas fa-sun text-yellow-500';
            }
            
            // Reload charts with new theme
            if (currentChart) renderChart(currentChartType);
            if (topChart) loadTopChart();
            if (pieChart) loadPieChart();
        }
        
        // Load saved theme on page load
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
            
            if (savedTheme === 'dark') {
                const slider = document.querySelector('.dark-mode-toggle-slider i');
                if (slider) slider.className = 'fas fa-moon text-yellow-400';
            }
        });
        
        // ============ EXPORT DATA ============
        function exportData(format) {
            const params = getFilterParams();
            
            // Build URL dengan route yang benar (sesuai structure: /dashboard/export/...)
            let url;
            if (format === 'excel') {
                url = `/dashboard/export/excel?${params}`;
            } else if (format === 'csv') {
                url = `/dashboard/export/csv?${params}`;
            } else if (format === 'pdf') {
                url = `/dashboard/export/pdf?${params}`;
            }
            
            console.log('Export URL:', url);
            
            // Show loading
            const btn = event.target.closest('button');
            const originalHTML = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Downloading...';
            btn.disabled = true;
            
            if (format === 'pdf') {
                // Untuk PDF, buka di tab baru agar bisa print to PDF
                window.open(url, '_blank');
                
                // Reset button immediately
                setTimeout(() => {
                    btn.innerHTML = originalHTML;
                    btn.disabled = false;
                }, 500);
            } else {
                // Simple redirect download untuk Excel dan CSV
                window.location.href = url;
                
                // Reset button after delay
                setTimeout(() => {
                    btn.innerHTML = originalHTML;
                    btn.disabled = false;
                }, 2000);
            }
        }
        
        function printDashboard() {
            // Print only the data table, not the whole page
            const printContent = generatePrintableReport();
            const printWindow = window.open('', 'PrintWindow', 'height=600,width=800');
            
            printWindow.document.open();
            printWindow.document.write(printContent);
            printWindow.document.close();
            
            // Wait for content and images to load then print
            printWindow.onload = function() {
                printWindow.focus();
                printWindow.print();
                // Close window after printing or if cancelled
                // printWindow.onafterprint = function() {
                //     printWindow.close();
                // };
            };
        }
        
        function generatePrintableReport() {
            // Get current filter values for display
            const shipperValues = shipperChoices.getValue(true);
            const bulanValues = bulanChoices.getValue(true);
            const tahunDari = document.querySelector('[name=tahun_dari]').value;
            const tahunSampai = document.querySelector('[name=tahun_sampai]').value;
            
            let filterInfo = '';
            if (shipperValues.length > 0) {
                filterInfo += `<p><strong>Shipper:</strong> ${shipperValues.join(', ')}</p>`;
            }
            if (tahunDari || tahunSampai) {
                filterInfo += `<p><strong>Periode:</strong> ${tahunDari || '...'} - ${tahunSampai || '...'}</p>`;
            }
            if (bulanValues.length > 0 && bulanValues.length < 12) {
                const monthNames = bulanValues.map(m => new Date(2000, m-1).toLocaleString('id-ID', {month: 'long'}));
                filterInfo += `<p><strong>Bulan:</strong> ${monthNames.join(', ')}</p>`;
            }
            
            // Clone the data table
            const table = document.querySelector('table').cloneNode(true);
            
            // Get KPI values
            const totalVolume = document.querySelectorAll('.stat-card h3')[0]?.textContent || '0';
            const totalRecords = document.querySelectorAll('.stat-card h3')[1]?.textContent || '0';
            const avgVolume = document.querySelectorAll('.stat-card h3')[2]?.textContent || '0';
            
            const currentDate = new Date().toLocaleString('id-ID', {
                day: '2-digit',
                month: 'long',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
            
            return `<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pertamina Gas Report - ${new Date().toLocaleDateString('id-ID')}</title>
    <style>
        @media print {
            @page { 
                margin: 1cm; 
                size: portrait;
            }
            body { margin: 0; }
            .no-print { display: none; }
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
            padding: 20px;
            background: white;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #D71920;
            padding-bottom: 15px;
        }
        .header h1 {
            color: #D71920;
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        .header p {
            margin: 5px 0;
            color: #666;
            font-size: 10px;
        }
        .summary {
            background-color: #f0f0f0;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 4px solid #D71920;
            page-break-inside: avoid;
        }
        .summary h3 {
            margin-top: 0;
            margin-bottom: 10px;
            color: #D71920;
            font-size: 14px;
        }
        .summary p {
            margin: 5px 0;
            font-size: 11px;
        }
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        .kpi-box {
            background: #fff;
            border: 2px solid #D71920;
            border-radius: 8px;
            padding: 10px;
            text-align: center;
        }
        .kpi-box .label {
            font-size: 9px;
            color: #666;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .kpi-box .value {
            font-size: 18px;
            font-weight: bold;
            color: #D71920;
            margin: 5px 0;
        }
        .kpi-box .unit {
            font-size: 8px;
            color: #999;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background-color: #D71920;
            color: white;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
        }
        td {
            padding: 6px 8px;
            border-bottom: 1px solid #ddd;
            font-size: 10px;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 9px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>PERTAMINA GAS</h1>
        <p>Dashboard Penyaluran Gas 2020-2025</p>
        <p>Laporan Data Penyaluran</p>
        <p>Generated: ${currentDate}</p>
    </div>
    
    <div class="kpi-grid">
        <div class="kpi-box">
            <div class="label">Total Volume Penyaluran</div>
            <div class="value">${totalVolume}</div>
            <div class="unit">MMSCFD</div>
        </div>
        <div class="kpi-box">
            <div class="label">Total Records</div>
            <div class="value">${totalRecords}</div>
            <div class="unit">Data Points</div>
        </div>
        <div class="kpi-box">
            <div class="label">Average Volume</div>
            <div class="value">${avgVolume}</div>
            <div class="unit">MMSCFD</div>
        </div>
    </div>
    
    ${filterInfo ? `
    <div class="summary">
        <h3>Filter yang Diterapkan:</h3>
        ${filterInfo}
    </div>
    ` : ''}
    
    ${table.outerHTML}
    
    <div class="footer">
        <p>&copy; ${new Date().getFullYear()} Pertamina Gas - Developed for PKL Program</p>
        <p>This is a computer-generated document. No signature is required.</p>
    </div>
</body>
</html>`;
        }
        
        // ============ PIE CHART ============
        function loadPieChart() {
            const params = getFilterParams();
            
            document.getElementById('pieLoading').style.display = 'flex';
            document.getElementById('pieChart').style.display = 'none';
            
            // Use all-shippers-data endpoint instead of top-data
            fetch("/all-shippers-data?" + params)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('pieLoading').style.display = 'none';
                    document.getElementById('pieChart').style.display = 'block';
                    
                    if (!data || data.length === 0) {
                        document.getElementById('pieChart').innerHTML = '<p class="text-center text-gray-400 py-8 text-sm font-semibold">Tidak ada data tersedia</p>';
                        return;
                    }

                    if (pieChart) {
                        pieChart.destroy();
                    }
                    
                    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
                    const textColor = isDark ? '#d1d5db' : '#374151';
                    
                    const totalVolume = data.reduce((sum, item) => sum + parseFloat(item.total_volume), 0);
                    
                    // Generate dynamic colors for all shippers
                    const baseColors = ['#D71920', '#f59e0b', '#10b981', '#3b82f6', '#8b5cf6', '#ec4899', '#14b8a6', '#f97316', '#6366f1', '#84cc16', '#f43f5e', '#06b6d4'];
                    const colors = [];
                    for (let i = 0; i < data.length; i++) {
                        colors.push(baseColors[i % baseColors.length]);
                    }
                    
                    const options = {
                        series: data.map(d => parseFloat(d.total_volume)),
                        chart: {
                            type: 'donut',
                            height: 350,
                            fontFamily: 'Plus Jakarta Sans, sans-serif',
                            background: 'transparent'
                        },
                        labels: data.map(d => d.shipper),
                        colors: colors,
                        legend: {
                            position: 'bottom',
                            labels: { colors: textColor },
                            fontSize: '12px',
                            itemMargin: {
                                horizontal: 8,
                                vertical: 4
                            }
                        },
                        plotOptions: {
                            pie: {
                                donut: {
                                    size: '65%',
                                    labels: {
                                        show: true,
                                        name: {
                                            show: true,
                                            fontSize: '14px',
                                            color: textColor
                                        },
                                        value: {
                                            show: true,
                                            fontSize: '20px',
                                            fontWeight: 'bold',
                                            color: textColor,
                                            formatter: function(val) {
                                                return parseFloat(val).toFixed(2);
                                            }
                                        },
                                        total: {
                                            show: true,
                                            label: 'Total Volume',
                                            color: textColor,
                                            fontSize: '14px',
                                            formatter: function() {
                                                return totalVolume.toFixed(2);
                                            }
                                        }
                                    }
                                }
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            formatter: function(val) {
                                return val.toFixed(1) + '%';
                            },
                            style: {
                                fontSize: '11px',
                                fontWeight: 'bold'
                            },
                            dropShadow: {
                                enabled: false
                            }
                        },
                        tooltip: {
                            theme: isDark ? 'dark' : 'light',
                            y: {
                                formatter: function(val) {
                                    const percent = ((val / totalVolume) * 100).toFixed(2);
                                    return val.toFixed(2) + ' MMSCFD (' + percent + '%)';
                                }
                            }
                        }
                    };

                    pieChart = new ApexCharts(document.querySelector("#pieChart"), options);
                    pieChart.render();
                    
                    // Update stats - show all shippers
                    let statsHTML = '<div class="space-y-2" style="max-height: 350px; overflow-y: auto;">';
                    data.forEach((item, index) => {
                        const percent = ((parseFloat(item.total_volume) / totalVolume) * 100).toFixed(1);
                        statsHTML += `
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                <div class="flex items-center gap-3">
                                    <div class="w-4 h-4 rounded" style="background: ${colors[index]}"></div>
                                    <span class="font-semibold text-sm">${item.shipper}</span>
                                </div>
                                <div class="text-right">
                                    <div class="font-bold text-sm">${parseFloat(item.total_volume).toFixed(2)} MMSCFD</div>
                                    <div class="text-xs text-gray-500">${percent}%</div>
                                </div>
                            </div>
                        `;
                    });
                    statsHTML += '</div>';
                    
                    // Add summary at top
                    statsHTML = `
                        <div class="mb-4 p-4 bg-gradient-to-r from-red-50 to-orange-50 rounded-lg border-l-4 border-red-600">
                            <div class="text-sm font-bold text-gray-700 mb-2">Ringkasan Distribusi</div>
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <div>
                                    <span class="text-gray-600">Total Shipper:</span>
                                    <span class="font-bold text-gray-900 ml-1">${data.length}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Total Volume:</span>
                                    <span class="font-bold text-gray-900 ml-1">${totalVolume.toFixed(2)} MMSCFD</span>
                                </div>
                            </div>
                        </div>
                    ` + statsHTML;
                    
                    document.getElementById('pieStats').innerHTML = statsHTML;
                })
                .catch(err => {
                    console.error(err);
                    document.getElementById('pieLoading').style.display = 'none';
                    document.getElementById('pieChart').innerHTML = '<p class="text-center text-red-500 py-8 text-sm font-semibold">Error loading pie chart</p>';
                });
        }

        // ============ INITIALIZE CHOICES.JS ============
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Multi-Select for Shipper
            const shipperElement = document.getElementById('filterShipper');
            shipperChoices = new Choices(shipperElement, {
                removeItemButton: true,
                searchEnabled: true,
                searchPlaceholderValue: 'Cari shipper...',
                noResultsText: 'Tidak ada shipper ditemukan',
                noChoicesText: 'Tidak ada pilihan',
                itemSelectText: '', // Hapus teks "Press to select"
                maxItemCount: -1,
                placeholder: true,
                placeholderValue: 'Pilih satu atau lebih shipper...',
                searchResultLimit: 20,
                shouldSort: false
            });

            // Initialize Multi-Select for Bulan
            const bulanElement = document.getElementById('filterBulan');
            bulanChoices = new Choices(bulanElement, {
                removeItemButton: true,
                searchEnabled: false,
                noResultsText: 'Tidak ada bulan ditemukan',
                noChoicesText: 'Tidak ada pilihan',
                itemSelectText: '',
                maxItemCount: -1,
                placeholder: true,
                placeholderValue: 'Pilih satu atau lebih bulan...',
                shouldSort: false
            });

            // Select All Shipper Button
            document.getElementById('selectAllShippers').addEventListener('click', function() {
                const allValues = Array.from(shipperElement.options).map(opt => opt.value);
                shipperChoices.setChoiceByValue(allValues);
            });

            // Clear All Shipper Button
            document.getElementById('clearAllShippers').addEventListener('click', function() {
                shipperChoices.removeActiveItems();
            });

            // Select All Bulan Button
            document.getElementById('selectAllBulan').addEventListener('click', function() {
                const allValues = Array.from(bulanElement.options).map(opt => opt.value);
                bulanChoices.setChoiceByValue(allValues);
            });

            // Clear All Bulan Button
            document.getElementById('clearAllBulan').addEventListener('click', function() {
                bulanChoices.removeActiveItems();
            });

            // Initial load
            loadMainChart();
            loadTopChart();
            loadPieChart();
            updateActiveFilters();
            document.getElementById('trendAnalysisSection').style.display = 'block';
        });

        function resetForm() {
            window.location.href = "{{ route('dashboard') }}";
        }

        // Display active filters
        function updateActiveFilters() {
            const filters = [];
            const form = document.getElementById('filterForm');
            
            const shipperValues = shipperChoices.getValue(true);
            const bulanValues = bulanChoices.getValue(true);
            const tahunDari = form.querySelector('[name=tahun_dari]').value;
            const tahunSampai = form.querySelector('[name=tahun_sampai]').value;
            
            console.log('Active Filters:');
            console.log('Shippers:', shipperValues);
            console.log('Bulan:', bulanValues);
            console.log('Tahun Dari:', tahunDari);
            console.log('Tahun Sampai:', tahunSampai);
            
            if (shipperValues && shipperValues.length > 0) {
                if (shipperValues.length === 1) {
                    filters.push({label: 'Shipper', value: shipperValues[0]});
                } else {
                    filters.push({label: 'Shipper', value: shipperValues.length + ' shipper dipilih'});
                }
            }
            
            if (bulanValues && bulanValues.length > 0) {
                if (bulanValues.length === 1) {
                    const monthName = new Date(2000, bulanValues[0] - 1).toLocaleString('id-ID', {month: 'long'});
                    filters.push({label: 'Bulan', value: monthName});
                } else {
                    filters.push({label: 'Bulan', value: bulanValues.length + ' bulan dipilih'});
                }
            }
            
            if (tahunDari) filters.push({label: 'Tahun Dari', value: tahunDari});
            if (tahunSampai) filters.push({label: 'Tahun Ke', value: tahunSampai});
            
            const container = document.getElementById('activeFilters');
            if (filters.length === 0) {
                container.innerHTML = '<span class="text-sm text-gray-400 italic">Tidak ada filter aktif</span>';
            } else {
                container.innerHTML = filters.map(f => 
                    `<span class="filter-chip">
                        <i class="fas fa-filter"></i>
                        <span>${f.label}: <strong>${f.value}</strong></span>
                    </span>`
                ).join('');
            }
        }

        // Get filter params - UPDATE untuk multi-select shipper dan bulan
        function getFilterParams() {
            const form = document.getElementById('filterForm');
            const params = new URLSearchParams();
            
            // Get multiple shipper values - send as array
            const shipperValues = shipperChoices.getValue(true);
            if (shipperValues && shipperValues.length > 0) {
                shipperValues.forEach(shipper => {
                    params.append('shipper[]', shipper);
                });
            }
            
            // Get multiple bulan values - send as array
            const bulanValues = bulanChoices.getValue(true);
            if (bulanValues && bulanValues.length > 0) {
                bulanValues.forEach(bulan => {
                    params.append('bulan[]', bulan);
                });
            }
            
            const tahunDari = form.querySelector('[name=tahun_dari]').value;
            const tahunSampai = form.querySelector('[name=tahun_sampai]').value;
            
            if (tahunDari) params.append('tahun_dari', tahunDari);
            if (tahunSampai) params.append('tahun_sampai', tahunSampai);
            
            return params.toString();
        }

        // ============ MAIN CHART (Trend) - MULTI SHIPPER ============
        function loadMainChart() {
            const params = getFilterParams();
            console.log('Loading chart with params:', params);
            console.log('Decoded params:', decodeURIComponent(params));
            
            fetch("{{ route('chart.data') }}?" + params)
                .then(res => res.json())
                .then(data => {
                    if (!data || !data.labels || data.labels.length === 0) {
                        document.getElementById('gasChart').innerHTML = '<div class="flex flex-col items-center justify-center py-16"><i class="fas fa-chart-line text-6xl text-gray-300 mb-4"></i><p class="text-center text-gray-500 font-semibold">Tidak ada data untuk ditampilkan</p></div>';
                        return;
                    }
                    
                    currentLabels = data.labels;
                    currentSeries = data.series;
                    renderChart(currentChartType);
                })
                .catch(err => {
                    console.error(err);
                    document.getElementById('gasChart').innerHTML = '<p class="text-center text-red-500 py-8 font-semibold">Error loading chart</p>';
                });
        }

        function renderChart(type) {
            if (currentChart) {
                currentChart.destroy();
                document.getElementById('gasChart').innerHTML = '';
            }

            if (!currentSeries || currentSeries.length === 0) {
                document.getElementById('gasChart').innerHTML = '<div class="flex flex-col items-center justify-center py-16"><i class="fas fa-chart-line text-6xl text-gray-300 mb-4"></i><p class="text-center text-gray-500 font-semibold">Tidak ada data untuk ditampilkan</p></div>';
                return;
            }

            const colors = ['#D71920', '#f59e0b', '#10b981', '#3b82f6', '#8b5cf6', '#ec4899', '#14b8a6', '#f97316', '#6366f1', '#84cc16'];
            const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
            const textColor = isDark ? '#d1d5db' : '#374151';
            const gridColor = isDark ? '#374151' : '#f1f1f1';

            const options = {
                series: currentSeries,
                chart: {
                    type: type,
                    height: 600,
                    toolbar: { show: true },
                    animations: { enabled: true, easing: 'easeinout', speed: 800 },
                    fontFamily: 'Plus Jakarta Sans, sans-serif',
                    background: 'transparent'
                },
                colors: colors,
                stroke: type === 'line' ? { curve: 'smooth', width: 2.5 } : { width: 0 },
                fill: {
                    type: 'solid',
                    opacity: type === 'line' ? 1 : 0.9
                },
                markers: type === 'line' ? { 
                    size: 5, 
                    hover: { size: 6, strokeColors: '#fff', strokeWidth: 2 } 
                } : {},
                xaxis: {
                    categories: currentLabels,
                    labels: { 
                        style: { fontSize: '11px', fontWeight: 600, colors: textColor }, 
                        rotate: currentLabels.length > 15 ? -35 : 0
                    }
                },
                yaxis: { 
                    title: { text: 'Volume (MMSCFD)', style: { fontWeight: 700, color: textColor } }, 
                    labels: { 
                        formatter: v => v.toFixed(1),
                        style: { colors: textColor }
                    } 
                },
                tooltip: { 
                    enabled: true,
                    shared: true,
                    intersect: false,
                    followCursor: true,
                    theme: isDark ? 'dark' : 'light',
                    style: {
                        fontSize: '12px',
                        fontFamily: 'Plus Jakarta Sans, sans-serif'
                    },
                    x: {
                        show: true
                    },
                    y: {
                        formatter: function(val) {
                            return val.toFixed(2) + ' MMSCFD';
                        }
                    },
                    marker: {
                        show: true
                    }
                },
                legend: {
                    position: 'bottom',
                    horizontalAlign: 'center',
                    fontSize: '12px',
                    fontWeight: 600,
                    markers: { width: 12, height: 12, radius: 3 },
                    labels: { colors: textColor }
                },
                grid: { borderColor: gridColor, strokeDashArray: 3 },
                plotOptions: type === 'bar' ? {
                    bar: { 
                        borderRadius: 6,
                        columnWidth: currentSeries.length > 5 ? '80%' : '60%'
                    }
                } : {},
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

        // ============ TOP 5 SHIPPER (FILTERED) ============
        function loadTopChart() {
            const params = getFilterParams();
            console.log('Loading top chart with params:', params);
            
            document.getElementById('topLoading').style.display = 'flex';
            document.getElementById('topChart').style.display = 'none';
            
            fetch("{{ route('top.data') }}?" + params)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('topLoading').style.display = 'none';
                    document.getElementById('topChart').style.display = 'block';
                    
                    if (!data || data.length === 0) {
                        document.getElementById('topChart').innerHTML = '<p class="text-center text-gray-400 py-8 text-sm font-semibold">Tidak ada data tersedia</p>';
                        return;
                    }

                    if (topChart) {
                        topChart.destroy();
                    }

                    const options = {
                        series: [{ name: 'Total Volume', data: data.map(d => parseFloat(d.total_volume)) }],
                        chart: { 
                            type: 'bar', 
                            height: 240, 
                            toolbar: { show: false },
                            fontFamily: 'Plus Jakarta Sans, sans-serif'
                        },
                        colors: ['#D71920','#f59e0b','#10b981','#3b82f6','#8b5cf6'],
                        plotOptions: { 
                            bar: { 
                                borderRadius: 8, 
                                distributed: true, 
                                columnWidth: '60%',
                                dataLabels: {
                                    position: 'top'
                                }
                            } 
                        },
                        xaxis: { 
                            categories: data.map(d => d.shipper),
                            labels: { style: { fontSize: '11px', fontWeight: 600 } }
                        },
                        yaxis: { 
                            labels: { formatter: v => v.toFixed(0) },
                            title: { text: 'Volume (MMSCFD)', style: {fontWeight: 700} }
                        },
                        legend: { show: false },
                        dataLabels: { 
                            enabled: true, 
                            formatter: v => v.toFixed(0), 
                            style: { fontSize: '11px', colors: ['#fff'], fontWeight: 'bold' },
                            offsetY: -20
                        },
                        tooltip: {
                            theme: 'dark',
                            y: {
                                formatter: function(val) {
                                    return val.toFixed(2) + " MMSCFD"
                                }
                            }
                        }
                    };

                    topChart = new ApexCharts(document.querySelector("#topChart"), options);
                    topChart.render();
                })
                .catch(err => {
                    console.error(err);
                    document.getElementById('topLoading').style.display = 'none';
                    document.getElementById('topChart').innerHTML = '<p class="text-center text-red-500 py-8 text-sm font-semibold">Error loading data</p>';
                });
        }

        // ============ TREND ANALYSIS ============
        document.getElementById('trendShipperSelect').addEventListener('change', function() {
            const shipper = this.value;
            
            if (!shipper) {
                document.getElementById('trendResults').innerHTML = '';
                return;
            }

            document.getElementById('trendAnalysisSection').style.display = 'block';
            document.getElementById('trendLoading').style.display = 'flex';
            document.getElementById('trendResults').innerHTML = '';

            fetch("{{ route('dashboard') }}/trend-analysis?shipper=" + encodeURIComponent(shipper))
                .then(res => res.json())
                .then(data => {
                    document.getElementById('trendLoading').style.display = 'none';
                    
                    if (data.error) {
                        document.getElementById('trendResults').innerHTML = `<p class="text-red-500 font-semibold">${data.error}</p>`;
                        return;
                    }

                    let html = `
                        <div class="mb-6 p-5 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border-2 border-blue-200">
                            <h3 class="font-bold text-lg text-gray-900 mb-2">
                                <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                                Ringkasan Analisis - ${data.shipper}
                            </h3>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600">Total Periode Analisis:</span>
                                    <span class="font-bold text-gray-900 ml-2">${data.total_periods}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Anomali Terdeteksi:</span>
                                    <span class="font-bold ${data.anomaly_count > 0 ? 'text-red-600' : 'text-green-600'} ml-2">
                                        ${data.anomaly_count} periode
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gradient-to-r from-gray-100 to-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-bold text-gray-700">Periode</th>
                                        <th class="px-4 py-3 text-right font-bold text-gray-700">Volume</th>
                                        <th class="px-4 py-3 text-right font-bold text-gray-700">Volume Sebelum</th>
                                        <th class="px-4 py-3 text-right font-bold text-gray-700">Selisih</th>
                                        <th class="px-4 py-3 text-right font-bold text-gray-700">Perubahan</th>
                                        <th class="px-4 py-3 text-center font-bold text-gray-700">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                    `;

                    data.trends.forEach(trend => {
                        const statusBadge = trend.is_anomaly 
                            ? `<span class="anomaly-badge px-3 py-1.5 text-xs font-bold bg-red-600 text-white rounded-full"><i class="fas fa-exclamation-triangle mr-1"></i>ANOMALI</span>`
                            : `<span class="px-3 py-1.5 text-xs font-bold bg-green-100 text-green-800 rounded-full"><i class="fas fa-check-circle mr-1"></i>NORMAL</span>`;

                        html += `
                            <tr class="hover:bg-gray-50 transition ${trend.is_anomaly ? 'bg-red-50' : ''}">
                                <td class="px-4 py-3 font-semibold text-gray-900">${trend.periode}</td>
                                <td class="px-4 py-3 text-right font-bold text-gray-900">${trend.volume.toLocaleString('id-ID', {minimumFractionDigits: 2})}</td>
                                <td class="px-4 py-3 text-right text-gray-600">${trend.previous_volume.toLocaleString('id-ID', {minimumFractionDigits: 2})}</td>
                                <td class="px-4 py-3 text-right font-semibold ${trend.change >= 0 ? 'text-green-600' : 'text-red-600'}">
                                    ${trend.change >= 0 ? '+' : ''}${trend.change.toLocaleString('id-ID', {minimumFractionDigits: 2})}
                                </td>
                                <td class="px-4 py-3 text-right font-bold ${trend.percent_change >= 0 ? 'text-green-600' : 'text-red-600'}">
                                    ${trend.percent_change >= 0 ? '+' : ''}${trend.percent_change.toFixed(2)}%
                                </td>
                                <td class="px-4 py-3 text-center">
                                    ${statusBadge}
                                </td>
                            </tr>
                        `;
                    });

                    html += `
                                </tbody>
                            </table>
                        </div>
                    `;

                    document.getElementById('trendResults').innerHTML = html;
                })
                .catch(err => {
                    console.error(err);
                    document.getElementById('trendLoading').style.display = 'none';
                    document.getElementById('trendResults').innerHTML = '<p class="text-red-500 font-semibold">Error loading trend analysis</p>';
                });
        });

        // Listen filter changes for dynamic update
        document.querySelectorAll('#filterForm select:not(#filterShipper):not(#filterBulan)').forEach(sel => {
            sel.addEventListener('change', () => {
                loadMainChart();
                loadTopChart();
                loadPieChart();
                updateActiveFilters();
            });
        });
        
        // Listen shipper changes
        document.getElementById('filterShipper').addEventListener('change', () => {
            loadMainChart();
            loadTopChart();
            loadPieChart();
            updateActiveFilters();
        });
        
        // Listen bulan changes
        document.getElementById('filterBulan').addEventListener('change', () => {
            loadMainChart();
            loadTopChart();
            loadPieChart();
            updateActiveFilters();
        });
    </script>
</body>
</html>
