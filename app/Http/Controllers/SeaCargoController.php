<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\Client;
use Illuminate\Http\Request;

class SeaCargoController extends Controller
{
    /**
     * Display a listing of sea cargo shipments
     */
    public function index(Request $request)
    {
        $query = Shipment::where('shipment_type', 'sea')
            ->with(['client', 'batch']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('tracking_number', 'like', "%{$search}%")
                  ->orWhereHas('client', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('current_status', $request->status);
        }

        $shipments = $query->latest()->paginate(20);

        return view('sea-cargo.index', compact('shipments'));
    }

    /**
     * Show the form for creating a new sea cargo shipment
     */
    public function create()
    {
        $clients = Client::orderBy('name')->get();
        return view('sea-cargo.create', compact('clients'));
    }

    /**
     * Store a newly created sea cargo shipment
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tracking_number' => 'nullable|string|max:255|unique:shipments,tracking_number',
            'client_id' => 'required|exists:clients,id',
            'origin' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'weight' => 'nullable|numeric|min:0',
            'charge_type' => 'nullable|in:per_kg,per_package,flat_rate,per_cbm',
            'delivery_time_min' => 'required|integer|min:1',
            'delivery_time_max' => 'required|integer|min:1|gte:delivery_time_min',
            'current_status' => 'nullable|string',
            'description' => 'nullable|string',
            'num_packages' => 'nullable|integer|min:1',
            'package_type' => 'nullable|in:box,pallet,envelope,custom',
            'fragile' => 'nullable|boolean',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'cbm' => 'nullable|numeric|min:0',
            'shipping_cost' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'total_amount' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'sender_name' => 'nullable|string|max:255',
            'sender_phone' => 'nullable|string|max:255',
            'sender_address' => 'nullable|string',
            'receiver_name' => 'nullable|string|max:255',
            'receiver_phone' => 'nullable|string|max:255',
            'receiver_address' => 'nullable|string',
            'receiver_id' => 'nullable|exists:clients,id',
            'payment_method' => 'nullable|string',
            'payment_status' => 'nullable|string',
        ]);

        $validated['shipment_type'] = 'sea';
        $validated['delivery_time_unit'] = 'months';
        $validated['current_status'] = $validated['current_status'] ?? 'Pending';

        // Calculate total
        if (isset($validated['shipping_cost'])) {
            $validated['total_amount'] = ($validated['shipping_cost'] ?? 0) + ($validated['tax'] ?? 0) - ($validated['discount'] ?? 0);
        }

        $shipment = Shipment::create($validated);

        // Auto-generate invoice for the shipment
        $invoice = \App\Models\Invoice::create([
            'shipment_id' => $shipment->id,
            'invoice_number' => \App\Models\Invoice::generateInvoiceNumber(),
            'issue_date' => now(),
            'due_date' => now()->addDays(30),
            'subtotal' => $shipment->shipping_cost ?? 0,
            'tax' => $shipment->tax ?? 0,
            'discount' => $shipment->discount ?? 0,
            'total' => $shipment->total_amount ?? 0,
            'status' => 'sent',
            'created_by' => auth()->id(),
        ]);

        if ($shipment->shipping_cost > 0) {
            $invoice->items()->create([
                'description' => 'Sea Cargo Service',
                'quantity' => 1,
                'rate' => $shipment->shipping_cost,
                'amount' => $shipment->shipping_cost,
                'order' => 0,
            ]);
        }

        if ($shipment->tax > 0) {
            $invoice->items()->create([
                'description' => 'Tax',
                'quantity' => 1,
                'rate' => $shipment->tax,
                'amount' => $shipment->tax,
                'order' => 1,
            ]);
        }

        return redirect()->route('admin.sea-cargo.show', $shipment)
            ->with('success', 'Sea cargo shipment created successfully. Tracking: ' . $shipment->tracking_number);
    }

    /**
     * Display the specified sea cargo shipment
     */
    public function show(Shipment $sea_cargo)
    {
        if ($sea_cargo->shipment_type !== 'sea') {
            abort(404);
        }

        $sea_cargo->load(['client', 'receiver', 'batch', 'statusUpdates', 'invoices.items', 'invoices.payments']);
        return view('sea-cargo.show', ['shipment' => $sea_cargo]);
    }

    /**
     * Show the form for editing the specified sea cargo shipment
     */
    public function edit(Shipment $sea_cargo)
    {
        if ($sea_cargo->shipment_type !== 'sea') {
            abort(404);
        }

        $clients = Client::orderBy('name')->get();
        return view('sea-cargo.edit', ['shipment' => $sea_cargo, 'clients' => $clients]);
    }

    /**
     * Update the specified sea cargo shipment
     */
    public function update(Request $request, Shipment $sea_cargo)
    {
        if ($sea_cargo->shipment_type !== 'sea') {
            abort(404);
        }

        $validated = $request->validate([
            'tracking_number' => 'required|string|max:255|unique:shipments,tracking_number,' . $sea_cargo->id,
            'client_id' => 'required|exists:clients,id',
            'origin' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'weight' => 'nullable|numeric|min:0',
            'charge_type' => 'nullable|in:per_kg,per_package,flat_rate,per_cbm',
            'delivery_time_min' => 'required|integer|min:1',
            'delivery_time_max' => 'required|integer|min:1|gte:delivery_time_min',
            'current_status' => 'nullable|string',
            'description' => 'nullable|string',
            'num_packages' => 'nullable|integer|min:1',
            'package_type' => 'nullable|in:box,pallet,envelope,custom',
            'fragile' => 'nullable|boolean',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'cbm' => 'nullable|numeric|min:0',
            'shipping_cost' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'sender_name' => 'nullable|string|max:255',
            'sender_phone' => 'nullable|string|max:255',
            'sender_address' => 'nullable|string',
            'receiver_name' => 'nullable|string|max:255',
            'receiver_phone' => 'nullable|string|max:255',
            'receiver_address' => 'nullable|string',
            'receiver_id' => 'nullable|exists:clients,id',
        ]);

        $validated['delivery_time_unit'] = 'months';

        // Calculate total
        if (isset($validated['shipping_cost'])) {
            $validated['total_amount'] = ($validated['shipping_cost'] ?? 0) + ($validated['tax'] ?? 0);
        }

        if (isset($validated['current_status']) && strtolower(trim($validated['current_status'])) === 'picked up') {
            $sea_cargo->load('invoices.items');
            $invoice = $sea_cargo->invoices->first();

            if ($invoice && $invoice->balance > 0) {
                return redirect()->back()->withInput()
                    ->with('error', 'Cannot change status to "Picked Up". The invoice has an outstanding balance. Please ensure the invoice is fully paid first.');
            }
        }

        $sea_cargo->update($validated);

        return redirect()->route('admin.sea-cargo.show', $sea_cargo)
            ->with('success', 'Sea cargo shipment updated successfully.');
    }

    /**
     * Remove the specified sea cargo shipment
     */
    public function destroy(Shipment $sea_cargo)
    {
        if ($sea_cargo->shipment_type !== 'sea') {
            abort(404);
        }

        $sea_cargo->delete();

        return redirect()->route('admin.sea-cargo.index')
            ->with('success', 'Sea cargo shipment deleted successfully.');
    }
}
