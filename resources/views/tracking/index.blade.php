<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Track Shipment - Bryan Logistics</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <!-- Navbar -->
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center gap-3">
                        <img class="h-12 w-auto object-contain" src="{{ asset('images/logo.png') }}" alt="Bryan Logistics">
                        <span class="text-2xl font-bold text-gray-900 tracking-tight">Bryan Logistics</span>
                    </div>
                </div>
                <div class="flex items-center">
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-blue-600 px-4 py-2 rounded-full text-sm font-medium transition-colors duration-200 hover:bg-blue-50">Login</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow flex items-center justify-center px-4 sm:px-6 lg:px-8 py-12">
        <div class="max-w-3xl w-full space-y-8">
            <div class="bg-white p-8 rounded-2xl shadow-lg border border-gray-100">
                <div class="text-center">
                    <div class="mx-auto h-16 w-16 bg-blue-50 rounded-full flex items-center justify-center mb-6">
                        <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                        </svg>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Track Your Shipment</h2>
                    <p class="mt-3 text-gray-600">
                        Enter your tracking number to get real-time updates on your package delivery.
                    </p>
                </div>

                <form class="mt-8" action="{{ route('tracking.result') }}" method="GET">
                    <div class="flex rounded-md shadow-sm">
                        <div class="relative flex-grow focus-within:z-10">
                            <input id="tracking_number" name="tracking_number" type="text" required 
                                class="focus:ring-blue-500 focus:border-blue-500 block w-full rounded-none rounded-l-lg pl-4 sm:text-lg border-2 border-gray-300 focus:bg-white bg-gray-50 py-4 transition-all duration-200" 
                                placeholder="Enter tracking number">
                        </div>
                        <button type="submit" class="-ml-px relative inline-flex items-center space-x-2 px-8 py-4 border border-transparent text-lg font-medium rounded-r-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-md transition-all duration-200 hover:shadow-lg">
                            <span>Track</span>
                            <svg class="h-5 w-5 text-blue-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>

                    @if($errors->any())
                        <div class="mt-4 rounded-lg bg-red-50 p-4 border border-red-100">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">
                                        {{ $errors->first() }}
                                    </h3>
                                </div>
                            </div>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-auto">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <p class="text-center text-sm text-gray-500">
                &copy; {{ date('Y') }} Bryan Logistics. All rights reserved.
            </p>
        </div>
    </footer>
</body>
</html>
