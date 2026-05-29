<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Track Shipment - LLC Express Logistics</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
        .bg-overlay {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.9) 0%, rgba(30, 58, 138, 0.85) 100%);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col" style="background-image: url('{{ asset('images/bg.png') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">
    <div class="bg-overlay min-h-screen flex flex-col">
        <!-- Navbar -->
        <nav class="glass-effect shadow-lg sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-20">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 flex items-center gap-3">
                            <div class="h-12 w-12 bg-gradient-to-br from-blue-600 to-blue-800 rounded-xl flex items-center justify-center shadow-lg">
                                <i class="fas fa-shipping-fast text-white text-xl"></i>
                            </div>
                            <span class="text-2xl font-bold text-gray-900 tracking-tight">LLC Express Logistics</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <a href="tel:+256703948463" class="hidden md:flex items-center gap-2 text-gray-700 hover:text-blue-600 px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 hover:bg-blue-50">
                            <i class="fas fa-phone-alt text-blue-600"></i>
                            <span>+256 703 948463</span>
                        </a>
                        <a href="{{ route('login') }}" class="flex items-center gap-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 hover:from-blue-700 hover:to-blue-800 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>Login</span>
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="flex-grow flex items-center justify-center px-4 sm:px-6 lg:px-8 py-16">
            <div class="max-w-4xl w-full space-y-10">
                <!-- Hero Section -->
                <div class="text-center space-y-4">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 rounded-2xl mb-4">
                        <i class="fas fa-box-open text-white text-3xl"></i>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-bold text-white tracking-tight">
                        Track Your Shipment
                    </h1>
                    <p class="text-base text-blue-100 max-w-2xl mx-auto">
                        Enter your tracking number below to get real-time updates on your package delivery status.
                    </p>
                </div>

                <!-- Search Card -->
                <div class="glass-effect rounded-3xl shadow-2xl p-6 md:p-8">
                    <form class="space-y-4" action="{{ route('tracking.result') }}" method="GET">
                        <div class="flex rounded-xl shadow-lg overflow-hidden">
                            <div class="relative flex-grow bg-gray-50/50">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <input id="tracking_number" name="tracking_number" type="text" required 
                                    class="block w-full pl-12 pr-4 py-3 text-base border-2 border-gray-200 border-r-0 rounded-l-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 bg-transparent transition-all duration-200 placeholder-gray-400"
                                    placeholder="Enter tracking number">
                            </div>
                            <button type="submit" class="flex items-center justify-center gap-2 px-6 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-medium rounded-r-xl transition-all duration-200 hover:from-blue-700 hover:to-blue-800 shadow-lg hover:shadow-xl">
                                <span>Track</span>
                                <i class="fas fa-location-arrow"></i>
                            </button>
                        </div>

                        @if($errors->any())
                            <div class="flex items-center gap-3 p-4 bg-red-50 border border-red-100 rounded-xl">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-red-800">{{ $errors->first() }}</p>
                                </div>
                            </div>
                        @endif
                    </form>
                </div>

                <!-- Features Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-12">
                    <div class="text-center space-y-3">
                        <div class="inline-flex items-center justify-center w-14 h-14 bg-white/20 rounded-2xl">
                            <i class="fas fa-clock text-white text-2xl"></i>
                        </div>
                        <h3 class="text-white font-semibold">Real-Time Tracking</h3>
                        <p class="text-blue-100 text-sm">Get instant updates on your shipment status 24/7</p>
                    </div>
                    <div class="text-center space-y-3">
                        <div class="inline-flex items-center justify-center w-14 h-14 bg-white/20 rounded-2xl">
                            <i class="fas fa-shield-alt text-white text-2xl"></i>
                        </div>
                        <h3 class="text-white font-semibold">Secure Delivery</h3>
                        <p class="text-blue-100 text-sm">Your packages are insured and safely handled</p>
                    </div>
                    <div class="text-center space-y-3">
                        <div class="inline-flex items-center justify-center w-14 h-14 bg-white/20 rounded-2xl">
                            <i class="fas fa-headset text-white text-2xl"></i>
                        </div>
                        <h3 class="text-white font-semibold">24/7 Support</h3>
                        <p class="text-blue-100 text-sm">Our team is always ready to help you</p>
                    </div>
                </div>
            </div>
        </main>

        <!-- Bottom Bar -->
        <div class="bg-black/20 backdrop-blur-sm">
            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <p class="text-blue-100 text-sm text-center md:text-left">
                        &copy; {{ date('Y') }} LLC Express Logistics. All rights reserved.
                    </p>
                    <div class="flex items-center gap-6">
                        <a href="#" class="text-blue-100 hover:text-white text-sm transition-colors"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-blue-100 hover:text-white text-sm transition-colors"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-blue-100 hover:text-white text-sm transition-colors"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-blue-100 hover:text-white text-sm transition-colors"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
