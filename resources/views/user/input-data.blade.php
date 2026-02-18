@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900">
            <i class="fas fa-plus-circle text-red-600 mr-2"></i>
            Input Data Manual
        </h1>
        <p class="text-sm text-gray-600 mt-1">Tambahkan data penyaluran atau penerimaan gas secara manual</p>
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

    <!-- Form Card -->
    <div class="max-w-4xl">
        <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
            <div class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-500 rounded-lg">
                <p class="text-sm font-semibold text-blue-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    Pastikan data yang Anda input akurat dan tidak duplikat dengan data yang sudah ada
                </p>
            </div>

            <form method="POST" action="{{ route('data.store') }}" class="space-y-6">
                @csrf

                <!-- Data Type -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fas fa-database text-red-500 mr-1"></i> Tipe Data
                        <span class="text-red-600">*</span>
                    </label>
                    <div class="flex gap-4">
                        <label class="flex items-center cursor-pointer p-4 border-2 border-gray-200 rounded-xl hover:border-red-500 transition flex-1">
                            <input 
                                type="radio" 
                                name="data" 
                                value="PENYALURAN" 
                                {{ old('data', 'PENYALURAN') == 'PENYALURAN' ? 'checked' : '' }}
                                class="w-4 h-4 text-red-600 border-gray-300 focus:ring-red-500"
                                required
                            >
                            <span class="ml-3 text-sm font-medium text-gray-700">
                                <i class="fas fa-arrow-right text-blue-500 mr-2"></i> PENYALURAN
                            </span>
                        </label>
                        <label class="flex items-center cursor-pointer p-4 border-2 border-gray-200 rounded-xl hover:border-red-500 transition flex-1">
                            <input 
                                type="radio" 
                                name="data" 
                                value="PENERIMAAN"
                                {{ old('data') == 'PENERIMAAN' ? 'checked' : '' }}
                                class="w-4 h-4 text-red-600 border-gray-300 focus:ring-red-500"
                            >
                            <span class="ml-3 text-sm font-medium text-gray-700">
                                <i class="fas fa-arrow-left text-green-500 mr-2"></i> PENERIMAAN
                            </span>
                        </label>
                    </div>
                </div>

                <!-- Shipper -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fas fa-building text-red-500 mr-1"></i> Shipper
                        <span class="text-red-600">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="shipper" 
                        value="{{ old('shipper') }}"
                        list="shipperList"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition font-medium"
                        placeholder="Masukkan nama shipper (e.g., PGN, PLN, PKG)"
                        required
                    >
                    <datalist id="shipperList">
                        @foreach($shippers as $shipper)
                        <option value="{{ $shipper }}">
                        @endforeach
                    </datalist>
                    <p class="text-xs text-gray-500 mt-1">Ketik atau pilih dari daftar existing shippers</p>
                </div>

                <!-- Tahun & Bulan -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-calendar text-red-500 mr-1"></i> Tahun
                            <span class="text-red-600">*</span>
                        </label>
                        <input 
                            type="number" 
                            name="tahun" 
                            value="{{ old('tahun', date('Y')) }}"
                            min="2020"
                            max="2030"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition font-medium"
                            placeholder="2025"
                            required
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-calendar-day text-red-500 mr-1"></i> Bulan
                            <span class="text-red-600">*</span>
                        </label>
                        <select 
                            name="bulan" 
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition font-medium"
                            required
                        >
                            <option value="">-- Pilih Bulan --</option>
                            @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ old('bulan') == $i ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                            </option>
                            @endfor
                        </select>
                    </div>
                </div>

                <!-- Volume -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fas fa-tachometer-alt text-red-500 mr-1"></i> Daily Average Volume (MMSCFD)
                        <span class="text-red-600">*</span>
                    </label>
                    <input 
                        type="number" 
                        name="daily_average_mmscfd" 
                        value="{{ old('daily_average_mmscfd') }}"
                        step="0.01"
                        min="0"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition font-medium"
                        placeholder="123.45"
                        required
                    >
                    <p class="text-xs text-gray-500 mt-1">Gunakan titik (.) untuk desimal, contoh: 123.45</p>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-3 pt-6 border-t border-gray-200">
                    <button 
                        type="submit" 
                        class="flex-1 pertamina-gradient text-white px-6 py-4 rounded-xl font-bold hover:opacity-90 transition shadow-lg"
                    >
                        <i class="fas fa-save mr-2"></i>
                        Simpan Data
                    </button>
                    <button 
                        type="reset" 
                        class="px-6 py-4 bg-gray-100 text-gray-700 rounded-xl font-bold hover:bg-gray-200 transition"
                    >
                        <i class="fas fa-redo mr-2"></i>
                        Reset
                    </button>
                </div>
            </form>
        </div>

        <!-- Quick Info -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-4 rounded-xl border border-blue-200">
                <h3 class="font-bold text-blue-900 mb-2">
                    <i class="fas fa-upload mr-2"></i>Upload Bulk Data
                </h3>
                <p class="text-sm text-blue-800 mb-3">Punya banyak data? Upload via CSV lebih cepat!</p>
                <a href="{{ route('data.upload') }}" class="inline-block bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-700 transition">
                    <i class="fas fa-file-csv mr-2"></i>Upload CSV
                </a>
            </div>

            <div class="bg-gradient-to-r from-green-50 to-green-100 p-4 rounded-xl border border-green-200">
                <h3 class="font-bold text-green-900 mb-2">
                    <i class="fas fa-history mr-2"></i>Lihat Riwayat
                </h3>
                <p class="text-sm text-green-800 mb-3">Cek data yang sudah Anda input</p>
                <a href="{{ route('my.submissions') }}" class="inline-block bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-green-700 transition">
                    <i class="fas fa-list mr-2"></i>My Submissions
                </a>
            </div>
        </div>
    </div>
</div>
@endsection