@extends('layouts.app')

@section('content')
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pertamina Gas - Comparison Analysis</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: #a4c5da;
        }
        .pertamina-gradient { 
            background: linear-gradient(135deg, #D71920 0%, #8B0000 100%); 
        }
        .stat-card {
            background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
            border-left: 4px solid #D71920;
        }
        .card-hover { 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
        }
        .card-hover:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 20px 40px rgba(215, 25, 32, 0.15); 
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
        .section-header {
            border-left: 4px solid #D71920;
            padding-left: 1rem;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body class="bg-gray-50">

    <!-- Header -->
    <header class="pertamina-gradient text-white shadow-2xl">
        <div class="container mx-auto px-4 md:px-6 py-5">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center space-x-3 md:space-x-4">
                    <div class="bg-white rounded-xl p-2.5 flex items-center justify-center shadow-lg">
                        @if(file_exists(public_path('images/logo3.png')))
                            <img src="{{ asset('images/logo3.png') }}" alt="Pertamina Gas" class="h-8 md:h-11 w-auto object-contain">
                        @else
                            <span class="text-red-600 font-bold text-xs md:text-sm px-2">PERTAMINA GAS</span>
                        @endif
                    </div>
                    <div>
                        <h1 class="text-xl md:text-3xl font-extrabold tracking-tight">Comparison Analysis</h1>
                        <p class="text-red-100 text-xs md:text-sm font-medium">Penerimaan vs Penyaluran Gas</p>
                    </div>
                </div>
                <a href="{{ route('dashboard') }}" class="bg-white text-red-600 px-4 py-2 rounded-lg hover:bg-red-50 transition text-sm font-semibold shadow-lg">
                    <i class="fas fa-arrow-left mr-2"></i>
                    <span class="hidden md:inline">Kembali ke Dashboard</span>
                    <span class="md:hidden">Back</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container mx-auto px-6 py-8">

        <!-- Filter Section -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
            <div class="section-header">
                <h2 class="text-2xl font-bold text-gray-900">
                    <i class="fas fa-filter text-red-600 mr-2"></i>
                    Filter Comparison
                </h2>
                <p class="text-sm text-gray-500 mt-1">Pilih shipper dan periode untuk analisis perbandingan</p>
            </div>

            <form id="comparisonForm" class="space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Shipper -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-building text-red-500 mr-1"></i> Shipper
                        </label>
                        <select name="shipper" id="filterShipper" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition font-medium">
                            <option value="">-- Semua Shipper --</option>
                            @foreach($shippers as $shipper)
                                <option value="{{ $shipper }}">{{ $shipper }}</option>
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
                                <option value="{{ $tahun }}">{{ $tahun }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tahun Ke -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-calendar text-red-500 mr-1"></i> Tahun Ke
                        </label>
                        <select name="tahun_sampai" id="filterTahunSampai" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition font-medium">
                            <option value="">-- Pilih Tahun --</option>
                            @foreach($tahuns as $tahun)
                                <option value="{{ $tahun }}">{{ $tahun }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Analyze Button -->
                    <div class="flex items-end">
                        <button type="button" onclick="analyzeData()" class="w-full pertamina-gradient text-white px-6 py-3 rounded-xl font-bold hover:opacity-90 transition shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <i class="fas fa-chart-line mr-2"></i> Analyze
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8" id="summaryCards">
            <!-- Total Penerimaan -->
            <div class="stat-card rounded-2xl p-6 shadow-lg card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-xs font-bold uppercase tracking-wider mb-1">Total Penerimaan</p>
                        <h3 class="text-3xl font-extrabold text-gray-900" id="totalPenerimaan">0.00</h3>
                        <p class="text-xs text-gray-500 mt-1 font-medium">MMSCFD</p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-blue-100 to-blue-200 rounded-2xl flex items-center justify-center shadow-md">
                        <i class="fas fa-arrow-down text-blue-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Total Penyaluran -->
            <div class="stat-card rounded-2xl p-6 shadow-lg card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-xs font-bold uppercase tracking-wider mb-1">Total Penyaluran</p>
                        <h3 class="text-3xl font-extrabold text-gray-900" id="totalPenyaluran">0.00</h3>
                        <p class="text-xs text-gray-500 mt-1 font-medium">MMSCFD</p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-green-100 to-green-200 rounded-2xl flex items-center justify-center shadow-md">
                        <i class="fas fa-arrow-up text-green-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Gap (Selisih) -->
            <div class="stat-card rounded-2xl p-6 shadow-lg card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-xs font-bold uppercase tracking-wider mb-1">Gap (Selisih)</p>
                        <h3 class="text-3xl font-extrabold" id="totalGap">0.00</h3>
                        <p class="text-xs text-gray-500 mt-1 font-medium">MMSCFD</p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-orange-100 to-orange-200 rounded-2xl flex items-center justify-center shadow-md">
                        <i class="fas fa-exchange-alt text-orange-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Efficiency Ratio -->
            <div class="stat-card rounded-2xl p-6 shadow-lg card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-xs font-bold uppercase tracking-wider mb-1">Efficiency Ratio</p>
                        <h3 class="text-3xl font-extrabold text-gray-900" id="efficiencyRatio">0.00%</h3>
                        <p class="text-xs text-gray-500 mt-1 font-medium">Penyaluran/Penerimaan</p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-purple-100 to-purple-200 rounded-2xl flex items-center justify-center shadow-md">
                        <i class="fas fa-percent text-purple-600 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            
            <!-- Pie Chart - Total Overview -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <div class="section-header border-0 p-0 m-0 mb-4">
                    <h2 class="text-xl font-bold text-gray-900">
                        <i class="fas fa-chart-pie text-red-600 mr-2"></i>
                        Total Penerimaan vs Penyaluran
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Distribusi keseluruhan</p>
                </div>
                <div id="totalPieChart" class="min-h-[350px]"></div>
                <div id="totalPieLoading" class="flex justify-center items-center py-12">
                    <div class="loading-spinner"></div>
                </div>
            </div>

            <!-- Bar Chart - Per Shipper -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <div class="section-header border-0 p-0 m-0 mb-4">
                    <h2 class="text-xl font-bold text-gray-900">
                        <i class="fas fa-chart-bar text-red-600 mr-2"></i>
                        Perbandingan Per Shipper
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Penerimaan vs Penyaluran</p>
                </div>
                <div id="shipperBarChart" class="min-h-[350px]"></div>
                <div id="barLoading" class="flex justify-center items-center py-12">
                    <div class="loading-spinner"></div>
                </div>
            </div>
        </div>

        <!-- Trend Chart (Line) -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <div class="section-header mb-0">
                    <h2 class="text-2xl font-bold text-gray-900">
                        <i class="fas fa-chart-line text-red-600 mr-2"></i>
                        Trend Penerimaan vs Penyaluran Over Time
                    </h2>
                    <p class="text-sm text-gray-500 mt-1" id="trendSubtitle">Analisis pergerakan historis</p>
                </div>
            </div>
            <div id="trendChart" style="min-height: 400px;"></div>
            <div id="trendLoading" class="flex justify-center items-center py-12">
                <div class="loading-spinner"></div>
            </div>
        </div>

        <!-- Gap Analysis Table -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
            <div class="section-header">
                <h2 class="text-2xl font-bold text-gray-900">
                    <i class="fas fa-table text-red-600 mr-2"></i>
                    Gap Analysis Per Shipper
                </h2>
                <p class="text-sm text-gray-500 mt-1">Detail selisih penerimaan dan penyaluran</p>
            </div>
            <div id="gapTableContainer"></div>
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
        let totalPieChart = null;
        let shipperBarChart = null;
        let trendChart = null;

        function getFilterParams() {
            const shipper = document.getElementById('filterShipper').value;
            const tahunDari = document.getElementById('filterTahunDari').value;
            const tahunSampai = document.getElementById('filterTahunSampai').value;
            
            const params = new URLSearchParams();
            if (shipper) params.append('shipper', shipper);
            if (tahunDari) params.append('tahun_dari', tahunDari);
            if (tahunSampai) params.append('tahun_sampai', tahunSampai);
            
            return params.toString();
        }

        function analyzeData() {
            loadSummary();
            loadTotalPie();
            loadShipperBar();
            loadTrendChart();
        }

        // Load Summary Cards
        function loadSummary() {
            const params = getFilterParams();
            
            fetch("/comparison-summary?" + params)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('totalPenerimaan').textContent = data.totalPenerimaan.toLocaleString('id-ID', {minimumFractionDigits: 2});
                    document.getElementById('totalPenyaluran').textContent = data.totalPenyaluran.toLocaleString('id-ID', {minimumFractionDigits: 2});
                    
                    const gapElement = document.getElementById('totalGap');
                    gapElement.textContent = data.totalGap.toLocaleString('id-ID', {minimumFractionDigits: 2});
                    gapElement.className = data.totalGap >= 0 ? 'text-3xl font-extrabold text-green-600' : 'text-3xl font-extrabold text-red-600';
                    
                    document.getElementById('efficiencyRatio').textContent = data.efficiencyRatio.toFixed(2) + '%';
                })
                .catch(err => console.error('Error loading summary:', err));
        }

        // Load Total Pie Chart
        function loadTotalPie() {
            const params = getFilterParams();
            
            document.getElementById('totalPieLoading').style.display = 'flex';
            document.getElementById('totalPieChart').style.display = 'none';
            
            fetch("/comparison-summary?" + params)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('totalPieLoading').style.display = 'none';
                    document.getElementById('totalPieChart').style.display = 'block';
                    
                    if (totalPieChart) {
                        totalPieChart.destroy();
                    }
                    
                    const options = {
                        series: [data.totalPenerimaan, data.totalPenyaluran],
                        chart: {
                            type: 'donut',
                            height: 350,
                            fontFamily: 'Plus Jakarta Sans, sans-serif'
                        },
                        labels: ['Penerimaan', 'Penyaluran'],
                        colors: ['#3b82f6', '#10b981'],
                        legend: {
                            position: 'bottom',
                            fontSize: '14px',
                            fontWeight: 600
                        },
                        plotOptions: {
                            pie: {
                                donut: {
                                    size: '65%',
                                    labels: {
                                        show: true,
                                        name: { fontSize: '16px', fontWeight: 'bold' },
                                        value: { fontSize: '20px', fontWeight: 'bold', formatter: val => parseFloat(val).toFixed(2) },
                                        total: {
                                            show: true,
                                            label: 'Total Volume',
                                            fontSize: '14px',
                                            fontWeight: 'bold',
                                            formatter: () => (data.totalPenerimaan + data.totalPenyaluran).toFixed(2)
                                        }
                                    }
                                }
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            formatter: function(val) {
                                return val.toFixed(1) + '%';
                            }
                        },
                        tooltip: {
                            y: {
                                formatter: val => val.toFixed(2) + ' MMSCFD'
                            }
                        }
                    };
                    
                    totalPieChart = new ApexCharts(document.querySelector("#totalPieChart"), options);
                    totalPieChart.render();
                })
                .catch(err => {
                    console.error(err);
                    document.getElementById('totalPieLoading').style.display = 'none';
                    document.getElementById('totalPieChart').innerHTML = '<p class="text-center text-red-500 py-8">Error loading chart</p>';
                });
        }

        // Load Shipper Bar Chart
        function loadShipperBar() {
            const params = getFilterParams();
            
            document.getElementById('barLoading').style.display = 'flex';
            document.getElementById('shipperBarChart').style.display = 'none';
            
            fetch("/comparison-per-shipper?" + params)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('barLoading').style.display = 'none';
                    document.getElementById('shipperBarChart').style.display = 'block';
                    
                    if (shipperBarChart) {
                        shipperBarChart.destroy();
                    }
                    
                    const options = {
                        series: [
                            { name: 'Penerimaan', data: data.penerimaan },
                            { name: 'Penyaluran', data: data.penyaluran }
                        ],
                        chart: {
                            type: 'bar',
                            height: 350,
                            fontFamily: 'Plus Jakarta Sans, sans-serif',
                            toolbar: { show: true }
                        },
                        colors: ['#3b82f6', '#10b981'],
                        plotOptions: {
                            bar: {
                                horizontal: false,
                                columnWidth: '60%',
                                borderRadius: 8,
                                dataLabels: { position: 'top' }
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            formatter: val => val.toFixed(0),
                            offsetY: -20,
                            style: { fontSize: '11px', colors: ['#304758'], fontWeight: 'bold' }
                        },
                        xaxis: {
                            categories: data.shippers,
                            labels: { style: { fontSize: '11px', fontWeight: 600 } }
                        },
                        yaxis: {
                            title: { text: 'Volume (MMSCFD)', style: { fontWeight: 700 } },
                            labels: { formatter: v => v.toFixed(0) }
                        },
                        legend: {
                            position: 'top',
                            fontSize: '13px',
                            fontWeight: 600
                        },
                        tooltip: {
                            shared: true,
                            intersect: false,
                            y: {
                                formatter: val => val.toFixed(2) + ' MMSCFD'
                            }
                        }
                    };
                    
                    shipperBarChart = new ApexCharts(document.querySelector("#shipperBarChart"), options);
                    shipperBarChart.render();
                    
                    // Update Gap Table
                    updateGapTable(data);
                })
                .catch(err => {
                    console.error(err);
                    document.getElementById('barLoading').style.display = 'none';
                    document.getElementById('shipperBarChart').innerHTML = '<p class="text-center text-red-500 py-8">Error loading chart</p>';
                });
        }

        // Load Trend Chart - FIXED ROUTE with smart shipper handling
        function loadTrendChart() {
            const params = getFilterParams();
            const selectedShipper = document.getElementById('filterShipper').value;
            
            document.getElementById('trendLoading').style.display = 'flex';
            document.getElementById('trendChart').style.display = 'none';
            
            // Update subtitle based on selection
            const subtitle = document.getElementById('trendSubtitle');
            if (selectedShipper) {
                subtitle.textContent = `Trend untuk ${selectedShipper}`;
            } else {
                subtitle.textContent = 'Analisis pergerakan historis - Semua Shipper (Aggregated)';
            }
            
            fetch("/dashboard/comparison-data?" + params)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('trendLoading').style.display = 'none';
                    document.getElementById('trendChart').style.display = 'block';
                    
                    if (trendChart) {
                        trendChart.destroy();
                    }
                    
                    const options = {
                        series: [
                            { name: 'Penerimaan', data: data.penerimaan },
                            { name: 'Penyaluran', data: data.penyaluran },
                            { name: 'Gap (Selisih)', data: data.gap }
                        ],
                        chart: {
                            type: 'line',
                            height: 400,
                            fontFamily: 'Plus Jakarta Sans, sans-serif',
                            toolbar: { 
                                show: true,
                                tools: {
                                    download: true,
                                    selection: true,
                                    zoom: true,
                                    zoomin: true,
                                    zoomout: true,
                                    pan: true,
                                    reset: true
                                }
                            },
                            animations: { enabled: true, easing: 'easeinout', speed: 800 }
                        },
                        colors: ['#3b82f6', '#10b981', '#f59e0b'],
                        stroke: { 
                            curve: 'smooth', 
                            width: [3, 3, 2],
                            dashArray: [0, 0, 5] // Gap line is dashed
                        },
                        markers: { 
                            size: [5, 5, 4], 
                            hover: { size: [7, 7, 6] } 
                        },
                        xaxis: {
                            categories: data.labels,
                            labels: { 
                                style: { fontSize: '11px', fontWeight: 600 },
                                rotate: -45,
                                rotateAlways: data.labels.length > 12
                            }
                        },
                        yaxis: {
                            title: { text: 'Volume (MMSCFD)', style: { fontWeight: 700 } },
                            labels: { formatter: v => v.toFixed(1) }
                        },
                        legend: {
                            position: 'top',
                            fontSize: '13px',
                            fontWeight: 600,
                            markers: {
                                width: 12,
                                height: 12,
                                radius: 2
                            }
                        },
                        tooltip: {
                            shared: true,
                            intersect: false,
                            y: {
                                formatter: val => val.toFixed(2) + ' MMSCFD'
                            }
                        },
                        grid: { 
                            borderColor: '#f1f1f1', 
                            strokeDashArray: 3,
                            xaxis: { lines: { show: true } },
                            yaxis: { lines: { show: true } }
                        },
                        annotations: {
                            yaxis: [{
                                y: 0,
                                borderColor: '#999',
                                borderWidth: 1,
                                strokeDashArray: 2,
                                label: {
                                    text: 'Zero Line',
                                    style: {
                                        color: '#666',
                                        fontSize: '10px'
                                    }
                                }
                            }]
                        }
                    };
                    
                    trendChart = new ApexCharts(document.querySelector("#trendChart"), options);
                    trendChart.render();
                })
                .catch(err => {
                    console.error(err);
                    document.getElementById('trendLoading').style.display = 'none';
                    document.getElementById('trendChart').innerHTML = '<p class="text-center text-red-500 py-8">Error loading chart</p>';
                });
        }

        // Update Gap Analysis Table
        function updateGapTable(data) {
            let html = '<div class="overflow-x-auto mt-4">';
            html += '<table class="w-full text-sm">';
            html += '<thead class="bg-gradient-to-r from-gray-100 to-gray-50">';
            html += '<tr>';
            html += '<th class="px-4 py-3 text-left font-bold text-gray-700">Shipper</th>';
            html += '<th class="px-4 py-3 text-right font-bold text-gray-700">Penerimaan (MMSCFD)</th>';
            html += '<th class="px-4 py-3 text-right font-bold text-gray-700">Penyaluran (MMSCFD)</th>';
            html += '<th class="px-4 py-3 text-right font-bold text-gray-700">Gap (MMSCFD)</th>';
            html += '<th class="px-4 py-3 text-right font-bold text-gray-700">Ratio (%)</th>';
            html += '</tr>';
            html += '</thead>';
            html += '<tbody class="divide-y divide-gray-100">';
            
            data.shippers.forEach((shipper, index) => {
                const penerimaan = data.penerimaan[index];
                const penyaluran = data.penyaluran[index];
                const gap = data.gap[index];
                const ratio = data.ratio[index];
                const gapClass = gap >= 0 ? 'text-green-600' : 'text-red-600';
                
                html += '<tr class="hover:bg-gray-50 transition">';
                html += `<td class="px-4 py-3 font-semibold text-gray-900">${shipper}</td>`;
                html += `<td class="px-4 py-3 text-right text-blue-600 font-bold">${penerimaan.toLocaleString('id-ID', {minimumFractionDigits: 2})}</td>`;
                html += `<td class="px-4 py-3 text-right text-green-600 font-bold">${penyaluran.toLocaleString('id-ID', {minimumFractionDigits: 2})}</td>`;
                html += `<td class="px-4 py-3 text-right font-bold ${gapClass}">${gap >= 0 ? '+' : ''}${gap.toLocaleString('id-ID', {minimumFractionDigits: 2})}</td>`;
                html += `<td class="px-4 py-3 text-right font-bold text-purple-600">${ratio.toFixed(2)}%</td>`;
                html += '</tr>';
            });
            
            html += '</tbody>';
            html += '</table>';
            html += '</div>';
            
            document.getElementById('gapTableContainer').innerHTML = html;
        }

        // Auto-load on page load
        document.addEventListener('DOMContentLoaded', function() {
            analyzeData();
        });
    </script>
</body>
</html>
@endsection