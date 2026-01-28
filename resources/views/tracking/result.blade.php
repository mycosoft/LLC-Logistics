<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tracking Result - {{ $shipment->tracking_number }}</title>
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
                        <a href="{{ route('tracking.index') }}" class="flex items-center gap-3">
                            <img class="h-10 w-auto object-contain" src="{{ asset('images/logo.png') }}" alt="Bryanz Logistics">
                            <span class="text-2xl font-bold text-gray-900 tracking-tight">Bryanz Logistics</span>
                        </a>
                    </div>
                </div>
                <div class="flex items-center">
                    <a href="{{ route('tracking.index') }}" class="text-gray-600 hover:text-blue-600 px-4 py-2 rounded-full text-sm font-medium transition-colors duration-200 hover:bg-blue-50 flex items-center gap-2">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Track Another
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white shadow-lg rounded-2xl overflow-hidden mb-8 border border-gray-100">
            <div class="px-6 py-8 sm:px-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-gray-100">
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Shipment Details</h3>
                    <p class="mt-1 text-3xl font-bold text-gray-900 tracking-tight">{{ $shipment->tracking_number }}</p>
                </div>
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                    <span class="w-2 h-2 bg-blue-600 rounded-full mr-2 animate-pulse"></span>
                    {{ $shipment->current_status }}
                </span>
            </div>
            <div class="px-6 py-6 sm:px-8">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Origin</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $shipment->origin }}</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Destination</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $shipment->destination }}</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Expected Delivery</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">
                            @if($shipment->delivery_time_min && $shipment->delivery_time_max)
                                {{ $shipment->delivery_time_min }}-{{ $shipment->delivery_time_max }} 
                                {{ $shipment->delivery_time_unit === 'months' ? 'months' : 'days' }}
                            @else
                                Not specified
                            @endif
                        </dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900 truncate">{{ $shipment->description ?? 'No description available' }}</dd>
                    </div>
                </dl>
            </div>
            <div class="px-6 py-6 sm:px-8 border-t border-gray-100">
                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4">Package Details</h4>
                <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Weight</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $shipment->weight ? $shipment->weight . ' kg' : 'N/A' }}</dd>
                    </div>

                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Packages</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $shipment->num_packages ?? 1 }}</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Service</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900 capitalize">{{ str_replace('_', ' ', $shipment->service_type ?? 'Standard') }}</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Type</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900 capitalize">{{ str_replace('_', ' ', $shipment->package_type ?? 'Box') }}</dd>
                    </div>
                </dl>
            </div>
        </div>





        <!-- Timeline -->
        <div class="bg-white shadow-lg rounded-2xl overflow-hidden border border-gray-100">
            <div class="px-6 py-6 sm:px-8 border-b border-gray-100">
                <h3 class="text-lg leading-6 font-bold text-gray-900">Tracking History</h3>
            </div>
            <div class="px-6 py-8 sm:px-8">
                <div class="flow-root">
                    <ul class="-mb-8">
                        @forelse($shipment->statusUpdates as $update)
                            <li>
                                <div class="relative pb-8">
                                    @if(!$loop->last)
                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex space-x-3">
                                        <div>
                                            @php
                                                $status = strtolower($update->status);
                                                $iconColor = 'bg-blue-600';
                                                $icon = '';
                                                
                                                if (str_contains($status, 'delivered')) {
                                                    $iconColor = 'bg-green-500';
                                                    $icon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />';
                                                } elseif (str_contains($status, 'transit') || str_contains($status, 'shipped')) {
                                                    $iconColor = 'bg-blue-600';
                                                    $icon = '<path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />';
                                                } elseif (str_contains($status, 'pending') || str_contains($status, 'created')) {
                                                    $iconColor = 'bg-gray-500';
                                                    $icon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />';
                                                } elseif (str_contains($status, 'hold') || str_contains($status, 'delayed')) {
                                                    $iconColor = 'bg-yellow-500';
                                                    $icon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />';
                                                } elseif (str_contains($status, 'cancelled') || str_contains($status, 'failed')) {
                                                    $iconColor = 'bg-red-500';
                                                    $icon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />';
                                                } else {
                                                    // Default
                                                    $icon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />';
                                                }
                                            @endphp
                                            
                                            <span class="h-10 w-10 rounded-full {{ $iconColor }} flex items-center justify-center ring-4 ring-white shadow-md">
                                                <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    {!! $icon !!}
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-base font-semibold text-gray-900">{{ $update->status }}</p>
                                                <p class="text-sm text-gray-500">at <span class="font-medium text-gray-700">{{ $update->location }}</span></p>
                                                @if($update->remarks)
                                                    <div class="mt-2 text-sm text-gray-600 bg-gray-50 p-3 rounded-lg border border-gray-100 inline-block">
                                                        {{ $update->remarks }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                <time datetime="{{ $update->created_at }}" class="font-medium">{{ $update->created_at->format('M d, H:i') }}</time>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li>
                                <div class="relative pb-8">
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-10 w-10 rounded-full bg-gray-400 flex items-center justify-center ring-4 ring-white">
                                                <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-base font-medium text-gray-500">Shipment created</p>
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                <time datetime="{{ $shipment->created_at }}">{{ $shipment->created_at->format('M d, H:i') }}</time>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-auto">
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <p class="text-center text-sm text-gray-500">
                &copy; {{ date('Y') }} Bryanz Logistics. All rights reserved.
            </p>
        </div>
    </footer>
</body>
</html>
