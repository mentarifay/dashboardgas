<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Pertamina Gas Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .pertamina-gradient {
            background: linear-gradient(135deg, #D71920 0%, #8B0000 100%);
        }
        .login-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }
        .login-card {
            background: white;
            border-radius: 1.5rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        }
        .input-group {
            position: relative;
        }
        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }
        .input-field {
            padding-left: 3rem;
        }
        .pulse-animation {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
    </style>
</head>
<body>
    <div class="login-container flex items-center justify-center p-4">
        <div class="w-full max-w-5xl">
            <div class="grid md:grid-cols-2 gap-0 login-card overflow-hidden">
                
                <!-- Left Panel - Branding -->
                <div class="pertamina-gradient p-12 text-white flex flex-col justify-center relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-5 rounded-full -mr-32 -mt-32"></div>
                    <div class="absolute bottom-0 left-0 w-96 h-96 bg-white opacity-5 rounded-full -ml-48 -mb-48"></div>
                    
                    <div class="relative z-10">
                        <!-- Logo -->
                        <div class="bg-white rounded-xl p-4 inline-block mb-8 shadow-lg">
                            @if(file_exists(public_path('images/logo3.png')))
                                <img src="{{ asset('images/logo3.png') }}" alt="Pertamina Gas" class="h-12 w-auto">
                            @else
                                <span class="text-red-600 font-bold text-xl px-2">PERTAMINA GAS</span>
                            @endif
                        </div>

                        <h1 class="text-4xl font-extrabold mb-4">
                            Welcome Back!
                        </h1>
                        <p class="text-red-100 text-lg mb-8">
                            Dashboard Penyaluran Gas 2020-2025
                        </p>

                        <div class="space-y-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-chart-line text-xl"></i>
                                </div>
                                <div>
                                    <div class="font-semibold">Real-time Monitoring</div>
                                    <div class="text-sm text-red-100">Track gas distribution live</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-shield-alt text-xl"></i>
                                </div>
                                <div>
                                    <div class="font-semibold">Secure Access</div>
                                    <div class="text-sm text-red-100">Role-based authentication</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-database text-xl"></i>
                                </div>
                                <div>
                                    <div class="font-semibold">Comprehensive Analytics</div>
                                    <div class="text-sm text-red-100">Advanced reporting tools</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Panel - Login Form -->
                <div class="p-12 flex flex-col justify-center">
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold text-gray-900 mb-2">Sign In</h2>
                        <p class="text-gray-600">Enter your credentials to access the dashboard</p>
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
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg pulse-animation">
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

                    <!-- Login Form -->
                    <form method="POST" action="{{ route('login.post') }}" class="space-y-6">
                        @csrf

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Email Address
                            </label>
                            <div class="input-group">
                                <i class="fas fa-envelope input-icon"></i>
                                <input 
                                    type="email" 
                                    name="email" 
                                    value="{{ old('email') }}"
                                    class="input-field w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition font-medium"
                                    placeholder="admin@pertamina.com"
                                    required
                                    autofocus
                                >
                            </div>
                        </div>

                        <!-- Password -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Password
                            </label>
                            <div class="input-group">
                                <i class="fas fa-lock input-icon"></i>
                                <input 
                                    type="password" 
                                    name="password" 
                                    id="password"
                                    class="input-field w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition font-medium"
                                    placeholder="••••••••"
                                    required
                                >
                                <button 
                                    type="button" 
                                    onclick="togglePassword()"
                                    class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                                >
                                    <i class="fas fa-eye" id="toggleIcon"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Remember Me -->
                        <div class="flex items-center justify-between">
                            <label class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    name="remember"
                                    class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500"
                                >
                                <span class="ml-2 text-sm text-gray-600 font-medium">Remember me</span>
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <button 
                            type="submit" 
                            class="w-full pertamina-gradient text-white px-6 py-4 rounded-xl font-bold text-lg hover:opacity-90 transition shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                        >
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Sign In
                        </button>
                    </form>

                    <!-- Demo Credentials -->
                    <div class="mt-8 p-4 bg-gray-50 rounded-xl border border-gray-200">
                        <p class="text-xs font-semibold text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                            Demo Credentials:
                        </p>
                        <div class="space-y-1 text-xs text-gray-600">
                            <div><strong>Admin:</strong> admin@pertamina.com / admin123</div>
                            <div><strong>User:</strong> user@pertamina.com / user123</div>
                            <div><strong>Viewer:</strong> viewer@pertamina.com / viewer123</div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="mt-8 text-center text-sm text-gray-500">
                        <p>&copy; {{ date('Y') }} Pertamina Gas. All rights reserved.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('[class*="bg-green-50"], [class*="bg-red-50"]');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
</body>
</html>