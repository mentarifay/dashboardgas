@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900">
                    <i class="fas fa-users text-red-600 mr-2"></i>
                    User Management
                </h1>
                <p class="text-sm text-gray-600 mt-1">Kelola pengguna dan hak akses sistem</p>
            </div>
            <a href="{{ route('users.create') }}" class="pertamina-gradient text-white px-6 py-3 rounded-xl font-bold hover:opacity-90 transition shadow-lg">
                <i class="fas fa-plus mr-2"></i>
                Tambah User Baru
            </a>
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

    @if(session('error'))
    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
            <p class="text-red-700 font-medium">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
        <form method="GET" action="{{ route('users.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-search text-red-500 mr-1"></i> Cari
                </label>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="Nama, email, atau phone..."
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition font-medium"
                >
            </div>

            <!-- Role Filter -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-user-tag text-red-500 mr-1"></i> Role
                </label>
                <select name="role" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition font-medium">
                    <option value="">Semua Role</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User</option>
                    <option value="viewer" {{ request('role') == 'viewer' ? 'selected' : '' }}>Viewer</option>
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-toggle-on text-red-500 mr-1"></i> Status
                </label>
                <select name="status" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition font-medium">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <!-- Actions -->
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-red-600 text-white px-4 py-3 rounded-xl font-bold hover:bg-red-700 transition">
                    <i class="fas fa-filter mr-2"></i> Filter
                </button>
                <a href="{{ route('users.index') }}" class="px-4 py-3 bg-gray-100 text-gray-700 rounded-xl font-bold hover:bg-gray-200 transition">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-gray-100 to-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">User</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Phone</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Joined</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($users as $user)
                    <tr class="hover:bg-red-50 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center text-white font-bold">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-bold text-gray-900">{{ $user->name }}</div>
                                    @if($user->id === auth()->id())
                                    <span class="text-xs text-blue-600 font-semibold">(You)</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            <i class="fas fa-envelope text-gray-400 mr-2"></i>
                            {{ $user->email }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            <i class="fas fa-phone text-gray-400 mr-2"></i>
                            {{ $user->phone ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($user->role === 'admin')
                            <span class="px-3 py-1.5 inline-flex text-xs font-bold rounded-full bg-gradient-to-r from-red-500 to-red-600 text-white shadow-sm">
                                <i class="fas fa-crown mr-1"></i> ADMIN
                            </span>
                            @elseif($user->role === 'user')
                            <span class="px-3 py-1.5 inline-flex text-xs font-bold rounded-full bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-sm">
                                <i class="fas fa-user mr-1"></i> USER
                            </span>
                            @else
                            <span class="px-3 py-1.5 inline-flex text-xs font-bold rounded-full bg-gradient-to-r from-gray-500 to-gray-600 text-white shadow-sm">
                                <i class="fas fa-eye mr-1"></i> VIEWER
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($user->status === 'active')
                            <span class="px-3 py-1.5 inline-flex text-xs font-bold rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i> Active
                            </span>
                            @else
                            <span class="px-3 py-1.5 inline-flex text-xs font-bold rounded-full bg-red-100 text-red-800">
                                <i class="fas fa-times-circle mr-1"></i> Inactive
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 text-center">
                            {{ $user->created_at->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('users.edit', $user->id) }}" class="px-3 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition text-xs font-bold">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($user->id !== auth()->id())
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition text-xs font-bold">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center">
                            <i class="fas fa-users text-6xl text-gray-300 mb-4"></i>
                            <p class="text-xl font-bold text-gray-500 mb-2">Tidak ada user ditemukan</p>
                            <p class="text-sm text-gray-400">Coba ubah filter pencarian Anda</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 bg-gradient-to-r from-gray-50 to-white">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>
@endsection