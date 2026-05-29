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
                            <img class="h-10 w-auto object-contain" src="{{ asset('images/logo.png') }}" alt="LLC Express Logistics">
                            <span class="text-2xl font-bold text-gray-900 tracking-tight">LLC Express Logistics</span>
                        </a>
            </div>
        </div>

        @if(strtolower($shipment->current_status) === 'ready for pickup')
        <div class="bg-amber-50 border-l-4 border-amber-500 p-4 mb-8 rounded-lg shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-bold text-amber-800">Package Ready for Pickup</p>
                    <p class="text-sm text-amber-700 mt-1">
                        Your package is ready for collection. <strong>Please collect it within 14 days.</strong> Uncollected packages after 2 weeks will be subject to auctioning.
                    </p>
                </div>
            </div>
        </div>
        @endif

        @if(strtolower($shipment->current_status) === 'auction warning')
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-8 rounded-lg shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-bold text-red-800">Auction Warning</p>
                    <p class="text-sm text-red-700 mt-1">
                        This package has been uncollected for over 14 days. If not picked up immediately, it will be moved to auction. Please contact us urgently.
                    </p>
                </div>
            </div>
        </div>
        @endif

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
                                                } elseif (str_contains($status, 'ready for pickup')) {
                                                    $iconColor = 'bg-teal-500';
                                                    $icon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
                                                } elseif (str_contains($status, 'auction')) {
                                                    $iconColor = 'bg-red-600';
                                                    $icon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />';
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
                &copy; {{ date('Y') }} LLC Express Logistics. All rights reserved.
            </p>
        </div>
    </footer>
</body>
</html>
