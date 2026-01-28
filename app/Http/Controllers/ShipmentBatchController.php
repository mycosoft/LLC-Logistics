<?php

namespace App\Http\Controllers;

use App\Models\ShipmentBatch;
use App\Models\Shipment;
use App\Models\Client;
use App\Http\Requests\StoreBatchRequest;
use App\Http\Requests\UpdateBatchStatusRequest;
use Illuminate\Http\Request;

class ShipmentBatchController extends Controller
{
    /**
     * Display a listing of batches
     */
    public function index(Request $request)
    {
        $query = ShipmentBatch::with(['shipments', 'creator']);

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('batch_number', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('current_status', $request->status);
        }

        $batches = $query->latest()->paginate(15);

        return view('batches.index', compact('batches'));
    }

    /**
     * Show the form for creating a new batch
     */
    public function create()
    {
        $clients = Client::orderBy('name')->get();
        
        return view('batches.create', compact('clients'));
    }

    /**
     * Store a newly created batch with multiple shipments
     */
    public function store(StoreBatchRequest $request)
    {
        // Create the batch
        $batch = ShipmentBatch::create([
            'name' => $request->name,
            'description' => $request->description,
            'cargo_type' => $request->cargo_type,
            'current_status' => $request->current_status,
            'created_by' => auth()->id(),
        ]);

        // Create shipments for this batch
        if ($request->has('shipments') && is_array($request->shipments)) {
            foreach ($request->shipments as $shipmentData) {
                // Add batch_id and current_status to shipment data
                $shipmentData['batch_id'] = $batch->id;
                $shipmentData['current_status'] = $batch->current_status;
                
                // Convert fragile checkbox to boolean
                $shipmentData['fragile'] = isset($shipmentData['fragile']) ? 1 : 0;
                
                // Calculate total_amount if pricing fields are present
                if (isset($shipmentData['shipping_cost'])) {
                    $shipping_cost = floatval($shipmentData['shipping_cost'] ?? 0);
                    $tax = floatval($shipmentData['tax'] ?? 0);
                    $discount = floatval($shipmentData['discount'] ?? 0);
                    $shipmentData['total_amount'] = $shipping_cost + $tax - $discount;
                }
                
                // Create the shipment
                $shipment = Shipment::create($shipmentData);
                
                // Create invoice for this shipment if pricing data exists
                if (isset($shipmentData['total_amount']) && $shipmentData['total_amount'] > 0) {
                    // Map payment_status to valid invoice status enum
                    $invoiceStatus = 'draft'; // Default to draft
                    if (isset($shipmentData['payment_status'])) {
                        if ($shipmentData['payment_status'] === 'paid') {
                            $invoiceStatus = 'paid';
                        }
                    }
                    
                    $invoice = \App\Models\Invoice::create([
                        'shipment_id' => $shipment->id,
                        'total' => $shipmentData['total_amount'],
                        'tax' => $shipmentData['tax'] ?? 0,
                        'discount' => $shipmentData['discount'] ?? 0,
                        'subtotal' => $shipmentData['shipping_cost'] ?? 0,
                        'status' => $invoiceStatus,
                        'issue_date' => now(),
                        'due_date' => now()->addDays(30),
                    ]);

                    // Create invoice items if they exist
                    if (isset($shipmentData['items']) && is_array($shipmentData['items'])) {
                        foreach ($shipmentData['items'] as $item) {
                            if (!empty($item['description'])) {
                                \App\Models\InvoiceItem::create([
                                    'invoice_id' => $invoice->id,
                                    'description' => $item['description'],
                                    'quantity' => $item['quantity'] ?? 1,
                                    'rate' => $item['rate'] ?? 0,
                                    'amount' => $item['amount'] ?? 0,
                                ]);
                            }
                        }
                    }
                }
            }
        }

        return redirect()->route('admin.batches.show', $batch)
            ->with('success', 'Batch created successfully with ' . count($request->shipments ?? []) . ' shipments. Batch Number: ' . $batch->batch_number);
    }

    /**
     * Display the specified batch
     */
    public function show(ShipmentBatch $batch)
    {
        $batch->load(['shipments.client', 'shipments.invoice', 'creator']);
        
        // Get available shipments (not in any batch) to add to this batch
        $availableShipments = Shipment::whereNull('batch_id')->with('client')->latest()->get();
        
        return view('batches.show', compact('batch', 'availableShipments'));
    }

    /**
     * Show the form for editing the batch
     */
    public function edit(ShipmentBatch $batch)
    {
        return view('batches.edit', compact('batch'));
    }

    /**
     * Update the batch information
     */
    public function update(Request $request, ShipmentBatch $batch)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $batch->update($validated);

        return redirect()->route('admin.batches.show', $batch)
            ->with('success', 'Batch updated successfully.');
    }

    /**
     * Update batch status (cascades to all shipments)
     */
    public function updateStatus(UpdateBatchStatusRequest $request, ShipmentBatch $batch)
    {
        $batch->updateBatchStatus($request->current_status, $request->location, $request->notes);

        return redirect()->route('admin.batches.show', $batch)
            ->with('success', 'Batch status updated successfully. All shipments in this batch have been updated.');
    }

    /**
     * Remove the batch (sets shipments' batch_id to null)
     */
    public function destroy(ShipmentBatch $batch)
    {
        // Remove batch association from shipments
        $batch->shipments()->update(['batch_id' => null]);
        
        $batch->delete();

        return redirect()->route('admin.batches.index')
            ->with('success', 'Batch deleted successfully.');
    }

    /**
     * Add shipment to batch
     */
    public function addShipment(Request $request, ShipmentBatch $batch)
    {
        $request->validate([
            'shipment_id' => 'required|exists:shipments,id',
        ]);

        $shipment = Shipment::find($request->shipment_id);
        $shipment->update(['batch_id' => $batch->id]);

        return redirect()->route('admin.batches.show', $batch)
            ->with('success', 'Shipment added to batch successfully.');
    }

    /**
     * Remove shipment from batch
     */
    public function removeShipment(ShipmentBatch $batch, Shipment $shipment)
    {
        $shipment->update(['batch_id' => null]);

        return redirect()->route('admin.batches.show', $batch)
            -with('success', 'Shipment removed from batch successfully.');
    }

    /**
     * Generate packing list PDF for batch
     */
    public function generatePackingList(ShipmentBatch $batch)
    {
        $batch->load(['shipments.client']);
        
        // Determine which template to use based on cargo type
        $template = $batch->cargo_type === 'sea' 
            ? 'batches.packing-list-sea' 
            : 'batches.packing-list-air';
        
        // Calculate totals
        $totalWeight = $batch->shipments->sum('weight');
        $totalPackages = $batch->shipments->sum('num_packages');
        $totalCBM = $batch->cargo_type === 'sea' ? $batch->shipments->sum('cbm') : 0;
        
        $pdf = \PDF::loadView($template, compact('batch', 'totalWeight', 'totalPackages', 'totalCBM'));
        
        $filename = 'Packing-List-' . $batch->batch_number . '.pdf';
        
        return $pdf->download($filename);
    }
}


