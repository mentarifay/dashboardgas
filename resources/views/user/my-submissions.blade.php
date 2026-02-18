@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900">
                    <i class="fas fa-list-alt text-red-600 mr-2"></i>
                    My Submissions
                </h1>
                <p class="text-sm text-gray-600 mt-1">Riwayat data yang Anda input ke sistem</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('data.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-xl font-bold hover:bg-blue-700 transition">
                    <i class="fas fa-plus mr-2"></i>Input Manual
                </a>
                <a href="{{ route('data.upload') }}" class="bg-green-600 text-white px-4 py-2 rounded-xl font-bold hover:bg-green-700 transition">
                    <i class="fas fa-upload mr-2"></i>Upload CSV
                </a>
            </div>
        </div>
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

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between mb-2">
                <i class="fas fa-database text-3xl opacity-80"></i>
                <span class="text-xs font-semibold opacity-80">TOTAL</span>
            </div>
            <h3 class="text-3xl font-extrabold">{{ $submissions->total() }}</h3>
            <p class="text-sm opacity-90 mt-1">Data Submitted</p>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between mb-2">
                <i class="fas fa-arrow-right text-3xl opacity-80"></i>
                <span class="text-xs font-semibold opacity-80">PENYALURAN</span>
            </div>
            <h3 class="text-3xl font-extrabold">
                {{ \App\Models\VolumeGas::where('created_by', auth()->id())->where('data', 'PENYALURAN')->count() }}
            </h3>
            <p class="text-sm opacity-90 mt-1">Records</p>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between mb-2">
                <i class="fas fa-arrow-left text-3xl opacity-80"></i>
                <span class="text-xs font-semibold opacity-80">PENERIMAAN</span>
            </div>
            <h3 class="text-3xl font-extrabold">
                {{ \App\Models\VolumeGas::where('created_by', auth()->id())->where('data', 'PENERIMAAN')->count() }}
            </h3>
            <p class="text-sm opacity-90 mt-1">Records</p>
        </div>

        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between mb-2">
                <i class="fas fa-clock text-3xl opacity-80"></i>
                <span class="text-xs font-semibold opacity-80">LATEST</span>
            </div>
            <h3 class="text-xl font-extrabold">
                @php
                    $latest = \App\Models\VolumeGas::where('created_by', auth()->id())->latest()->first();
                @endphp
                {{ $latest ? $latest->created_at->diffForHumans() : '-' }}
            </h3>
            <p class="text-sm opacity-90 mt-1">Last Submission</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
        <form method="GET" action="{{ route('my.submissions') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Data Type Filter -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-database text-red-500 mr-1"></i> Tipe Data
                </label>
                <select name="data" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition font-medium">
                    <option value="">Semua Tipe</option>
                    <option value="PENYALURAN" {{ request('data') == 'PENYALURAN' ? 'selected' : '' }}>PENYALURAN</option>
                    <option value="PENERIMAAN" {{ request('data') == 'PENERIMAAN' ? 'selected' : '' }}>PENERIMAAN</option>
                </select>
            </div>

            <!-- Shipper Filter -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-building text-red-500 mr-1"></i> Shipper
                </label>
                <input 
                    type="text" 
                    name="shipper" 
                    value="{{ request('shipper') }}"
                    placeholder="Cari shipper..."
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition font-medium"
                >
            </div>

            <!-- Year Filter -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-calendar text-red-500 mr-1"></i> Tahun
                </label>
                <select name="tahun" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition font-medium">
                    <option value="">Semua Tahun</option>
                    @foreach($years as $year)
                    <option value="{{ $year }}" {{ request('tahun') == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Actions -->
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-red-600 text-white px-4 py-3 rounded-xl font-bold hover:bg-red-700 transition">
                    <i class="fas fa-filter mr-2"></i> Filter
                </button>
                <a href="{{ route('my.submissions') }}" class="px-4 py-3 bg-gray-100 text-gray-700 rounded-xl font-bold hover:bg-gray-200 transition">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Submissions Table -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-gray-100 to-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Submitted At</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Shipper</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Periode</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Volume (MMSCFD)</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($submissions as $item)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-medium">
                            <i class="fas fa-clock text-gray-400 mr-2"></i>
                            {{ $item->created_at->format('d M Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($item->data === 'PENYALURAN')
                            <span class="px-3 py-1.5 inline-flex text-xs font-bold rounded-full bg-blue-100 text-blue-800">
                                <i class="fas fa-arrow-right mr-1"></i> PENYALURAN
                            </span>
                            @else
                            <span class="px-3 py-1.5 inline-flex text-xs font-bold rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-arrow-left mr-1"></i> PENERIMAAN
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-bold text-gray-900">{{ $item->shipper }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1.5 inline-flex text-xs font-bold rounded-full bg-gradient-to-r from-amber-100 to-yellow-100 text-amber-800 shadow-sm">
                                {{ $item->periode }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <span class="text-sm font-extrabold text-gray-900 bg-gray-100 px-3 py-1.5 rounded-lg">
                                {{ number_format($item->daily_average_mmscfd, 2) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <form action="{{ route('data.delete', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition text-xs font-bold">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                            <p class="text-xl font-bold text-gray-500 mb-2">Belum ada data</p>
                            <p class="text-sm text-gray-400 mb-4">Mulai input data untuk melihat riwayat Anda</p>
                            <div class="flex gap-3 justify-center">
                                <a href="{{ route('data.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-blue-700 transition text-sm">
                                    <i class="fas fa-plus mr-2"></i>Input Manual
                                </a>
                                <a href="{{ route('data.upload') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-green-700 transition text-sm">
                                    <i class="fas fa-upload mr-2"></i>Upload CSV
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($submissions->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 bg-gradient-to-r from-gray-50 to-white">
            {{ $submissions->links() }}
        </div>
        @endif
    </div>
</div>
@endsection