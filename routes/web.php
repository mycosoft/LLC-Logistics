<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

// Public Tracking Routes
Route::get('/track', [App\Http\Controllers\TrackingController::class, 'index'])->name('tracking.index');
Route::get('/track/result', [App\Http\Controllers\TrackingController::class, 'track'])->name('tracking.result');

// Dashboard - accessible by both admin and staff
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'role:admin,staff'])->name('dashboard');

// Profile routes - accessible by authenticated users
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin-only routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Admin routes will be added here

    // User & Role Management (Admin only)
    Route::resource('users', App\Http\Controllers\UserController::class);
    Route::resource('roles', App\Http\Controllers\RoleController::class);
    
    // Client Import (must be before clients resource route)
    Route::get('clients/import', [App\Http\Controllers\ClientImportController::class, 'showImportForm'])->name('clients.import');
    Route::post('clients/import', [App\Http\Controllers\ClientImportController::class, 'import'])->name('clients.import.process');
    Route::get('clients/import/template', [App\Http\Controllers\ClientImportController::class, 'downloadTemplate'])->name('clients.import.template');
    
    // Client Management (Admin only)
    Route::resource('clients', App\Http\Controllers\ClientController::class);
    
    // Air Cargo Management
    Route::resource('air-cargo', App\Http\Controllers\AirCargoController::class);
    
    // Sea Cargo Management
    Route::resource('sea-cargo', App\Http\Controllers\SeaCargoController::class);
    
    // Shipment Management (Admin only)
    Route::get('shipments/{shipment}/label', [App\Http\Controllers\ShipmentController::class, 'label'])->name('shipments.label');
    Route::get('shipments/{shipment}/invoice', [App\Http\Controllers\ShipmentController::class, 'invoice'])->name('shipments.invoice');
    Route::resource('shipments', App\Http\Controllers\ShipmentController::class);

    // Invoice Management
    Route::resource('invoices', App\Http\Controllers\InvoiceController::class);
    Route::get('invoices/{invoice}/pdf', [App\Http\Controllers\InvoiceController::class, 'generatePDF'])->name('invoices.pdf');
    Route::post('invoices/{invoice}/send', [App\Http\Controllers\InvoiceController::class, 'sendInvoice'])->name('invoices.send');
    
    // Payment Management
    Route::post('invoices/{invoice}/payments', [App\Http\Controllers\PaymentController::class, 'store'])->name('payments.store');
    Route::get('payments/{payment}/receipt', [App\Http\Controllers\PaymentController::class, 'generateReceipt'])->name('payments.receipt');
    Route::post('payments/{payment}/send', [App\Http\Controllers\PaymentController::class, 'sendReceipt'])->name('payments.send');
    Route::resource('payments', App\Http\Controllers\PaymentController::class)->except(['create', 'edit', 'update']);
    
    // Shipment Status Updates (Admin only)
    Route::post('shipment-status-updates', [App\Http\Controllers\ShipmentStatusUpdateController::class, 'store'])->name('shipment-status-updates.store');
    
    // Bulk Broadcast
    Route::get('broadcast', [App\Http\Controllers\BroadcastController::class, 'index']);
    Route::post('broadcast/send', [App\Http\Controllers\BroadcastController::class, 'send']);
    
    // Reports
    Route::get('reports', [App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/revenue', [App\Http\Controllers\ReportController::class, 'revenue'])->name('reports.revenue');
    Route::get('reports/payments', [App\Http\Controllers\ReportController::class, 'payments'])->name('reports.payments');
    Route::get('reports/outstanding', [App\Http\Controllers\ReportController::class, 'outstanding'])->name('reports.outstanding');
    Route::get('reports/shipments', [App\Http\Controllers\ReportController::class, 'shipments'])->name('reports.shipments');
    Route::get('reports/clients', [App\Http\Controllers\ReportController::class, 'clients'])->name('reports.clients');
    Route::get('reports/analytics', [App\Http\Controllers\ReportController::class, 'analytics'])->name('reports.analytics');
    Route::get('reports/shipments/pdf', [App\Http\Controllers\ReportController::class, 'exportShipmentsPdf'])->name('reports.shipments.pdf');
    Route::get('reports/clients/pdf', [App\Http\Controllers\ReportController::class, 'exportClientsPdf'])->name('reports.clients.pdf');
    
    // Settings
    Route::get('settings', [App\Http\Controllers\SettingController::class, 'index']);
    Route::put('settings', [App\Http\Controllers\SettingController::class, 'update']);
    
    // Batch Management
    Route::resource('batches', App\Http\Controllers\ShipmentBatchController::class);
    Route::post('batches/{batch}/update-status', [App\Http\Controllers\ShipmentBatchController::class, 'updateStatus'])->name('batches.update-status');
    Route::post('batches/{batch}/add-shipment', [App\Http\Controllers\ShipmentBatchController::class, 'addShipment'])->name('batches.add-shipment');
    Route::delete('batches/{batch}/shipments/{shipment}', [App\Http\Controllers\ShipmentBatchController::class, 'removeShipment'])->name('batches.remove-shipment');
    Route::get('batches/{batch}/packing-list', [App\Http\Controllers\ShipmentBatchController::class, 'generatePackingList'])->name('batches.packing-list');
});

// Staff and Admin routes
Route::middleware(['auth', 'role:admin,staff'])->group(function () {
    // Shared routes for both admin and staff will be added here
    // Client Management
    Route::resource('clients', App\Http\Controllers\ClientController::class);
    // Shipment Management
    Route::resource('shipments', App\Http\Controllers\ShipmentController::class);
    Route::post('shipments/{shipment}/updates', [App\Http\Controllers\ShipmentStatusUpdateController::class, 'store'])->name('shipments.updates.store');
});

require __DIR__.'/auth.php';
