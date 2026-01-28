<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Bryan Logistics</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }
        .bg-navy-900 {
            background-color: #0a192f;
        }
        .text-navy-900 {
            color: #0a192f;
        }
        .bg-navy-800 {
            background-color: #112240;
        }
        .hover\:bg-navy-800:hover {
            background-color: #112240;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-100" style="background-image: url('{{ asset('images/bg.png') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">
    <div class="max-w-md w-full space-y-8 p-8 bg-white rounded-xl shadow-lg">
        <!-- Logo and Header -->
        <div class="text-center">
            <div class="flex justify-center mb-6">
                <!-- Assuming logo is in public/images/logo.png -->
                <img src="{{ asset('images/logo.png') }}" alt="Bryanz Logistics Logo" class="h-40 w-auto object-contain">
            </div>
            <h2 class="text-3xl font-bold text-gray-900">
                Welcome Back
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                Sign in to manage your shipments
            </p>
        </div>

        <!-- Session Status -->
        @if (session('status'))
            <div class="p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-6">
            @csrf

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">
                    Email Address
                </label>
                <div class="mt-1">
                    <input 
                        id="email" 
                        type="email" 
                        name="email" 
                        value="{{ old('email') }}" 
                        required 
                        autofocus 
                        autocomplete="username"
                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-navy-900 focus:border-navy-900 sm:text-sm @error('email') border-red-500 @enderror"
                        placeholder="Enter your email address"
                    >
                </div>
                @error('email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">
                    Password
                </label>
                <div class="mt-1">
                    <input 
                        id="password" 
                        type="password" 
                        name="password" 
                        required 
                        autocomplete="current-password"
                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-navy-900 focus:border-navy-900 sm:text-sm @error('password') border-red-500 @enderror"
                        placeholder="••••••••"
                    >
                </div>
                @error('password')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input 
                        id="remember_me" 
                        type="checkbox" 
                        name="remember"
                        class="h-4 w-4 text-navy-900 focus:ring-navy-900 border-gray-300 rounded"
                    >
                    <label for="remember_me" class="ml-2 block text-sm text-gray-900">
                        Remember me
                    </label>
                </div>

                @if (Route::has('password.request'))
                    <div class="text-sm">
                        <a href="{{ route('password.request') }}" class="font-medium text-navy-900 hover:text-navy-800">
                            Forgot your password?
                        </a>
                    </div>
                @endif
            </div>

            <!-- Submit Button -->
            <div>
                <button 
                    type="submit"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-navy-900 hover:bg-navy-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-navy-900 transition duration-150 ease-in-out"
                    style="background-color: #0a192f;"
                >
                    Sign in
                </button>
            </div>
        </form>

        <!-- Footer -->
        <div class="mt-6 text-center">
            <p class="text-xs text-gray-500">
                &copy; {{ date('Y') }} Bryanz Logistics. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
