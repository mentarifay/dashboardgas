@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900">
            <i class="fas fa-file-upload text-red-600 mr-2"></i>
            Upload Data CSV
        </h1>
        <p class="text-sm text-gray-600 mt-1">Import data dalam jumlah besar menggunakan file CSV</p>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-lg">
        <div class="flex items-center">
            <i class="fas fa-check-circle text-green-500 mr-3"></i>
            <p class="text-green-700 font-medium">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('warning'))
    <div class="mb-6 p-4 bg-yellow-50 border-l-4 border-yellow-500 rounded-lg">
        <div class="flex items-center">
            <i class="fas fa-exclamation-triangle text-yellow-500 mr-3"></i>
            <p class="text-yellow-700 font-medium">{{ session('warning') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
            <p class="text-red-700 font-medium">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    @if($errors->any())
    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
        <div class="flex items-start">
            <i class="fas fa-exclamation-triangle text-red-500 mr-3 mt-1"></i>
            <div>
                <p class="text-red-700 font-medium mb-2">Terdapat kesalahan:</p>
                <ul class="list-disc list-inside text-sm text-red-600">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Upload Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
                <form method="POST" action="{{ route('data.upload.post') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- Data Type -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-database text-red-500 mr-1"></i> Tipe Data
                            <span class="text-red-600">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="flex items-center cursor-pointer p-4 border-2 border-gray-200 rounded-xl hover:border-red-500 transition">
                                <input 
                                    type="radio" 
                                    name="data_type" 
                                    value="PENYALURAN" 
                                    {{ old('data_type', 'PENYALURAN') == 'PENYALURAN' ? 'checked' : '' }}
                                    class="w-4 h-4 text-red-600 border-gray-300 focus:ring-red-500"
                                    required
                                >
                                <span class="ml-3 text-sm font-medium text-gray-700">
                                    <i class="fas fa-arrow-right text-blue-500 mr-2"></i> PENYALURAN
                                </span>
                            </label>
                            <label class="flex items-center cursor-pointer p-4 border-2 border-gray-200 rounded-xl hover:border-red-500 transition">
                                <input 
                                    type="radio" 
                                    name="data_type" 
                                    value="PENERIMAAN"
                                    {{ old('data_type') == 'PENERIMAAN' ? 'checked' : '' }}
                                    class="w-4 h-4 text-red-600 border-gray-300 focus:ring-red-500"
                                >
                                <span class="ml-3 text-sm font-medium text-gray-700">
                                    <i class="fas fa-arrow-left text-green-500 mr-2"></i> PENERIMAAN
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- File Upload -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-file-csv text-red-500 mr-1"></i> File CSV
                            <span class="text-red-600">*</span>
                        </label>
                        <div class="relative">
                            <input 
                                type="file" 
                                name="csv_file" 
                                id="csvFile"
                                accept=".csv"
                                class="hidden"
                                required
                                onchange="updateFileName()"
                            >
                            <label 
                                for="csvFile" 
                                class="flex flex-col items-center justify-center w-full h-48 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-red-500 hover:bg-red-50 transition"
                            >
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <i class="fas fa-cloud-upload-alt text-5xl text-gray-400 mb-4"></i>
                                    <p class="mb-2 text-sm font-semibold text-gray-700">
                                        <span class="text-red-600">Click to upload</span> or drag and drop
                                    </p>
                                    <p class="text-xs text-gray-500">CSV file (MAX. 2MB)</p>
                                    <p id="fileName" class="mt-2 text-sm font-bold text-blue-600"></p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Instructions -->
                    <div class="p-4 bg-yellow-50 border-l-4 border-yellow-500 rounded-lg">
                        <p class="text-sm font-semibold text-yellow-800 mb-2">
                            <i class="fas fa-info-circle mr-2"></i>
                            Format CSV yang benar:
                        </p>
                        <ul class="text-sm text-yellow-700 list-disc list-inside space-y-1">
                            <li>Header: <code class="bg-yellow-100 px-1 rounded">shipper,tahun,bulan,daily_average_mmscfd</code></li>
                            <li>Contoh: <code class="bg-yellow-100 px-1 rounded">PGN,2025,1,150.25</code></li>
                            <li>Tahun harus antara 2020-2030</li>
                            <li>Bulan harus antara 1-12</li>
                            <li>Volume tidak boleh negatif</li>
                            <li>Data duplikat akan dilewati</li>
                        </ul>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-3 pt-6 border-t border-gray-200">
                        <button 
                            type="submit" 
                            class="flex-1 pertamina-gradient text-white px-6 py-4 rounded-xl font-bold hover:opacity-90 transition shadow-lg"
                        >
                            <i class="fas fa-upload mr-2"></i>
                            Upload & Import
                        </button>
                        <a 
                            href="{{ route('data.create') }}" 
                            class="px-6 py-4 bg-gray-100 text-gray-700 rounded-xl font-bold hover:bg-gray-200 transition"
                        >
                            <i class="fas fa-times mr-2"></i>
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Instructions & Template -->
        <div class="space-y-6">
            <!-- Download Template -->
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-lg p-6 text-white">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-download text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="font-bold text-lg">Download Template</h3>
                        <p class="text-sm text-blue-100">Template CSV untuk upload</p>
                    </div>
                </div>
                <button 
                    onclick="downloadTemplate()" 
                    class="w-full bg-white text-blue-600 px-4 py-3 rounded-xl font-bold hover:bg-blue-50 transition"
                >
                    <i class="fas fa-file-download mr-2"></i>
                    Download Template CSV
                </button>
            </div>

            <!-- Example Data -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <h3 class="font-bold text-gray-900 mb-4">
                    <i class="fas fa-table text-red-600 mr-2"></i>
                    Contoh Data CSV
                </h3>
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <pre class="text-xs font-mono text-gray-700 overflow-x-auto">shipper,tahun,bulan,daily_average_mmscfd
PGN,2025,1,150.25
PKG,2025,1,142.50
PLN,2025,1,138.75
IAE,2025,1,45.30
BBG,2025,1,32.15</pre>
                </div>
            </div>

            <!-- Tips -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <h3 class="font-bold text-gray-900 mb-4">
                    <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
                    Tips Upload
                </h3>
                <ul class="space-y-2 text-sm text-gray-700">
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                        <span>Gunakan Excel, lalu Save As → CSV UTF-8</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                        <span>Pastikan tidak ada spasi di header</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                        <span>Desimal pakai titik (.), bukan koma</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                        <span>Maksimal 2MB per file</span>
                    </li>
                </ul>
            </div>

            <!-- Quick Link -->
            <div class="bg-gradient-to-r from-green-50 to-green-100 p-4 rounded-xl border border-green-200">
                <h3 class="font-bold text-green-900 mb-2">
                    <i class="fas fa-history mr-2"></i>Lihat Riwayat
                </h3>
                <p class="text-sm text-green-800 mb-3">Cek data yang sudah Anda upload</p>
                <a href="{{ route('my.submissions') }}" class="inline-block bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-green-700 transition">
                    <i class="fas fa-list mr-2"></i>My Submissions
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function updateFileName() {
    const input = document.getElementById('csvFile');
    const fileName = document.getElementById('fileName');
    
    if (input.files.length > 0) {
        fileName.textContent = '📄 ' + input.files[0].name;
    } else {
        fileName.textContent = '';
    }
}

function downloadTemplate() {
    const csvContent = `shipper,tahun,bulan,daily_average_mmscfd
PGN,2025,1,150.25
PKG,2025,1,142.50
PLN,2025,1,138.75
IAE,2025,1,45.30
BBG,2025,1,32.15
KEIL,2025,1,28.50
PERTAMINA,2025,1,15.75
SCE,2025,1,12.40
SNR,2025,1,10.20
IKD,2025,1,9.17
PGN JARGAS,2025,1,5.30
PTGN,2025,1,4.85`;

    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    link.setAttribute('href', url);
    link.setAttribute('download', 'template_gas_volume.csv');
    link.style.visibility = 'hidden';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>
@endsection