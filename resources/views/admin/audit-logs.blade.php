@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900">
            <i class="fas fa-history text-red-600 mr-2"></i>
            Audit Logs
        </h1>
        <p class="text-sm text-gray-600 mt-1">Riwayat aktivitas pengguna dalam sistem</p>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
        <form method="GET" action="{{ route('audit.logs') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <!-- Action Filter -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-bolt text-red-500 mr-1"></i> Action
                </label>
                <select name="action" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition font-medium">
                    <option value="">Semua Action</option>
                    @foreach($actions as $action)
                    <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                        {{ ucfirst($action) }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Table Filter -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-table text-red-500 mr-1"></i> Table
                </label>
                <select name="table_name" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition font-medium">
                    <option value="">Semua Table</option>
                    @foreach($tables as $table)
                    <option value="{{ $table }}" {{ request('table_name') == $table ? 'selected' : '' }}>
                        {{ $table }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Date From -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-calendar text-red-500 mr-1"></i> Dari Tanggal
                </label>
                <input 
                    type="date" 
                    name="date_from" 
                    value="{{ request('date_from') }}"
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition font-medium"
                >
            </div>

            <!-- Date To -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-calendar text-red-500 mr-1"></i> Sampai Tanggal
                </label>
                <input 
                    type="date" 
                    name="date_to" 
                    value="{{ request('date_to') }}"
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition font-medium"
                >
            </div>

            <!-- Actions -->
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-red-600 text-white px-4 py-3 rounded-xl font-bold hover:bg-red-700 transition">
                    <i class="fas fa-filter mr-2"></i> Filter
                </button>
                <a href="{{ route('audit.logs') }}" class="px-4 py-3 bg-gray-100 text-gray-700 rounded-xl font-bold hover:bg-gray-200 transition">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Audit Logs Table -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-gray-100 to-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Timestamp</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">User</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Action</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Table</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Record ID</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">IP Address</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Details</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-medium">
                            <i class="fas fa-clock text-gray-400 mr-2"></i>
                            {{ $log->created_at->format('d M Y H:i:s') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($log->user)
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center text-white font-bold text-xs">
                                    {{ strtoupper(substr($log->user->name, 0, 1)) }}
                                </div>
                                <div class="ml-2">
                                    <div class="text-sm font-bold text-gray-900">{{ $log->user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $log->user->email }}</div>
                                </div>
                            </div>
                            @else
                            <span class="text-sm text-gray-400 italic">User deleted</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($log->action === 'create')
                            <span class="px-3 py-1.5 inline-flex text-xs font-bold rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-plus mr-1"></i> CREATE
                            </span>
                            @elseif($log->action === 'update')
                            <span class="px-3 py-1.5 inline-flex text-xs font-bold rounded-full bg-blue-100 text-blue-800">
                                <i class="fas fa-edit mr-1"></i> UPDATE
                            </span>
                            @elseif($log->action === 'delete')
                            <span class="px-3 py-1.5 inline-flex text-xs font-bold rounded-full bg-red-100 text-red-800">
                                <i class="fas fa-trash mr-1"></i> DELETE
                            </span>
                            @elseif($log->action === 'login')
                            <span class="px-3 py-1.5 inline-flex text-xs font-bold rounded-full bg-purple-100 text-purple-800">
                                <i class="fas fa-sign-in-alt mr-1"></i> LOGIN
                            </span>
                            @elseif($log->action === 'logout')
                            <span class="px-3 py-1.5 inline-flex text-xs font-bold rounded-full bg-gray-100 text-gray-800">
                                <i class="fas fa-sign-out-alt mr-1"></i> LOGOUT
                            </span>
                            @else
                            <span class="px-3 py-1.5 inline-flex text-xs font-bold rounded-full bg-yellow-100 text-yellow-800">
                                {{ strtoupper($log->action) }}
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            @if($log->table_name)
                            <span class="px-2 py-1 bg-gray-100 rounded text-xs font-mono">{{ $log->table_name }}</span>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            @if($log->record_id)
                            <span class="px-2 py-1 bg-blue-50 text-blue-700 rounded text-xs font-mono">#{{ $log->record_id }}</span>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-mono">
                            <i class="fas fa-network-wired text-gray-400 mr-2"></i>
                            {{ $log->ip_address }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($log->old_value || $log->new_value)
                            <button 
                                onclick="showDetails({{ $log->id }})"
                                class="px-3 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition text-xs font-bold"
                            >
                                <i class="fas fa-eye mr-1"></i> View
                            </button>
                            @else
                            <span class="text-gray-400 text-xs">No details</span>
                            @endif
                        </td>
                    </tr>

                    <!-- Hidden Details Row -->
                    <tr id="details-{{ $log->id }}" class="hidden bg-gray-50">
                        <td colspan="7" class="px-6 py-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @if($log->old_value)
                                <div>
                                    <h4 class="text-sm font-bold text-gray-700 mb-2">
                                        <i class="fas fa-history text-red-500 mr-1"></i> Old Value:
                                    </h4>
                                    <pre class="bg-white p-3 rounded-lg border border-gray-200 text-xs overflow-auto max-h-64">{{ json_encode($log->old_value, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                                @endif
                                @if($log->new_value)
                                <div>
                                    <h4 class="text-sm font-bold text-gray-700 mb-2">
                                        <i class="fas fa-check text-green-500 mr-1"></i> New Value:
                                    </h4>
                                    <pre class="bg-white p-3 rounded-lg border border-gray-200 text-xs overflow-auto max-h-64">{{ json_encode($log->new_value, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                                @endif
                            </div>
                            <div class="mt-3 text-xs text-gray-600">
                                <strong>User Agent:</strong> {{ $log->user_agent }}
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center">
                            <i class="fas fa-history text-6xl text-gray-300 mb-4"></i>
                            <p class="text-xl font-bold text-gray-500 mb-2">Tidak ada log ditemukan</p>
                            <p class="text-sm text-gray-400">Coba ubah filter pencarian Anda</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 bg-gradient-to-r from-gray-50 to-white">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>

<script>
function showDetails(logId) {
    const detailsRow = document.getElementById('details-' + logId);
    if (detailsRow.classList.contains('hidden')) {
        detailsRow.classList.remove('hidden');
    } else {
        detailsRow.classList.add('hidden');
    }
}
</script>
@endsection