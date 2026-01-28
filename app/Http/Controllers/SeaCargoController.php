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
            'client_id' => 'required|exists:clients,id',
            'origin' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'weight' => 'nullable|numeric|min:0',
            'delivery_time_min' => 'required|integer|min:1',
            'delivery_time_max' => 'required|integer|min:1|gte:delivery_time_min',
            'current_status' => 'nullable|string',
            'description' => 'nullable|string',
            'num_packages' => 'nullable|integer|min:1',
            'package_type' => 'nullable|in:box,pallet,envelope,custom',
            'fragile' => 'nullable|boolean',
            'shipping_cost' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
        ]);

        $validated['shipment_type'] = 'sea';
        $validated['delivery_time_unit'] = 'months';
        $validated['current_status'] = $validated['current_status'] ?? 'Pending';
        
        // Calculate total
        if (isset($validated['shipping_cost'])) {
            $validated['total_amount'] = ($validated['shipping_cost'] ?? 0) + ($validated['tax'] ?? 0) - ($validated['discount'] ?? 0);
        }

        $shipment = Shipment::create($validated);

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

        $sea_cargo->load(['client', 'receiver', 'batch', 'statusUpdates']);
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
            'client_id' => 'required|exists:clients,id',
            'origin' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'weight' => 'nullable|numeric|min:0',
            'delivery_time_min' => 'required|integer|min:1',
            'delivery_time_max' => 'required|integer|min:1|gte:delivery_time_min',
            'current_status' => 'nullable|string',
            'description' => 'nullable|string',
            'num_packages' => 'nullable|integer|min:1',
            'package_type' => 'nullable|in:box,pallet,envelope,custom',
            'fragile' => 'nullable|boolean',
            'shipping_cost' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
        ]);

        $validated['delivery_time_unit'] = 'months';
        
        // Calculate total
        if (isset($validated['shipping_cost'])) {
            $validated['total_amount'] = ($validated['shipping_cost'] ?? 0) + ($validated['tax'] ?? 0) - ($validated['discount'] ?? 0);
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
