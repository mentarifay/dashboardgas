@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center gap-4 mb-4">
            <a href="{{ route('users.index') }}" class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center hover:bg-gray-200 transition">
                <i class="fas fa-arrow-left text-gray-600"></i>
            </a>
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900">
                    <i class="fas fa-user-plus text-red-600 mr-2"></i>
                    Tambah User Baru
                </h1>
                <p class="text-sm text-gray-600 mt-1">Buat akun pengguna baru untuk sistem</p>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="max-w-3xl">
        <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
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

            <form method="POST" action="{{ route('users.store') }}" class="space-y-6">
                @csrf

                <!-- Name -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fas fa-user text-red-500 mr-1"></i> Nama Lengkap
                        <span class="text-red-600">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="name" 
                        value="{{ old('name') }}"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition font-medium"
                        placeholder="John Doe"
                        required
                    >
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fas fa-envelope text-red-500 mr-1"></i> Email
                        <span class="text-red-600">*</span>
                    </label>
                    <input 
                        type="email" 
                        name="email" 
                        value="{{ old('email') }}"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition font-medium"
                        placeholder="john.doe@pertamina.com"
                        required
                    >
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fas fa-phone text-red-500 mr-1"></i> Nomor Telepon
                    </label>
                    <input 
                        type="text" 
                        name="phone" 
                        value="{{ old('phone') }}"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition font-medium"
                        placeholder="081234567890"
                    >
                </div>

                <!-- Password -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-lock text-red-500 mr-1"></i> Password
                            <span class="text-red-600">*</span>
                        </label>
                        <div class="relative">
                            <input 
                                type="password" 
                                name="password" 
                                id="password"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition font-medium"
                                placeholder="••••••••"
                                required
                            >
                            <button 
                                type="button" 
                                onclick="togglePassword('password', 'toggleIcon1')"
                                class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                            >
                                <i class="fas fa-eye" id="toggleIcon1"></i>
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Minimal 6 karakter</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-lock text-red-500 mr-1"></i> Konfirmasi Password
                            <span class="text-red-600">*</span>
                        </label>
                        <div class="relative">
                            <input 
                                type="password" 
                                name="password_confirmation"
                                id="password_confirmation"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition font-medium"
                                placeholder="••••••••"
                                required
                            >
                            <button 
                                type="button" 
                                onclick="togglePassword('password_confirmation', 'toggleIcon2')"
                                class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                            >
                                <i class="fas fa-eye" id="toggleIcon2"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Role -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fas fa-user-tag text-red-500 mr-1"></i> Role
                        <span class="text-red-600">*</span>
                    </label>
                    <select 
                        name="role" 
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition font-medium"
                        required
                    >
                        <option value="">-- Pilih Role --</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin (Full Access)</option>
                        <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User (Data Entry)</option>
                        <option value="viewer" {{ old('role') == 'viewer' ? 'selected' : '' }}>Viewer (Read Only)</option>
                    </select>
                    <div class="mt-2 p-3 bg-blue-50 rounded-lg border border-blue-200">
                        <p class="text-xs text-blue-800 font-semibold mb-1">
                            <i class="fas fa-info-circle mr-1"></i> Penjelasan Role:
                        </p>
                        <ul class="text-xs text-blue-700 space-y-1">
                            <li><strong>Admin:</strong> Kelola user, hapus data, akses penuh</li>
                            <li><strong>User:</strong> Input & edit data, upload file</li>
                            <li><strong>Viewer:</strong> Hanya melihat dashboard & laporan</li>
                        </ul>
                    </div>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fas fa-toggle-on text-red-500 mr-1"></i> Status
                        <span class="text-red-600">*</span>
                    </label>
                    <div class="flex gap-4">
                        <label class="flex items-center cursor-pointer">
                            <input 
                                type="radio" 
                                name="status" 
                                value="active" 
                                {{ old('status', 'active') == 'active' ? 'checked' : '' }}
                                class="w-4 h-4 text-red-600 border-gray-300 focus:ring-red-500"
                            >
                            <span class="ml-2 text-sm font-medium text-gray-700">
                                <i class="fas fa-check-circle text-green-500 mr-1"></i> Active
                            </span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input 
                                type="radio" 
                                name="status" 
                                value="inactive"
                                {{ old('status') == 'inactive' ? 'checked' : '' }}
                                class="w-4 h-4 text-red-600 border-gray-300 focus:ring-red-500"
                            >
                            <span class="ml-2 text-sm font-medium text-gray-700">
                                <i class="fas fa-times-circle text-red-500 mr-1"></i> Inactive
                            </span>
                        </label>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-3 pt-6 border-t border-gray-200">
                    <button 
                        type="submit" 
                        class="flex-1 pertamina-gradient text-white px-6 py-4 rounded-xl font-bold hover:opacity-90 transition shadow-lg"
                    >
                        <i class="fas fa-save mr-2"></i>
                        Simpan User
                    </button>
                    <a 
                        href="{{ route('users.index') }}" 
                        class="px-6 py-4 bg-gray-100 text-gray-700 rounded-xl font-bold hover:bg-gray-200 transition"
                    >
                        <i class="fas fa-times mr-2"></i>
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function togglePassword(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
@endsection