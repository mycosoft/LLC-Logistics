<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\Client;
use App\Http\Requests\StoreShipmentRequest;
use App\Http\Requests\UpdateShipmentRequest;
use Illuminate\Http\Request;

class ShipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Shipment::with('client');

        // Filter by client
        if ($request->has('client_id') && $request->client_id != '') {
            $query->where('client_id', $request->client_id);
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('current_status', $request->status);
        }

        // Search by tracking number
        if ($request->has('tracking_number') && $request->tracking_number != '') {
            $query->where('tracking_number', 'like', '%' . $request->tracking_number . '%');
        }

        // General search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('tracking_number', 'like', "%{$search}%")
                  ->orWhere('origin', 'like', "%{$search}%")
                  ->orWhere('destination', 'like', "%{$search}%");
            });
        }

        $shipments = $query->latest()->paginate(15);
        $clients = Client::orderBy('name')->get();

        return view('shipments.index', compact('shipments', 'clients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clients = Client::orderBy('name')->get();
        return view('shipments.create', compact('clients'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreShipmentRequest $request)
    {
        $shipment = Shipment::create($request->validated());

        // Create invoice with line items if items are provided
        if ($request->has('items') && is_array($request->items)) {
            $invoice = \App\Models\Invoice::create([
                'shipment_id' => $shipment->id,
                'invoice_number' => \App\Models\Invoice::generateInvoiceNumber(),
                'issue_date' => now(),
                'due_date' => now()->addDays(30),
                'subtotal' => $request->shipping_cost ?? 0,
                'tax' => $request->tax ?? 0,
                'discount' => $request->discount ?? 0,
                'total' => $request->total_amount ?? 0,
                'status' => $request->payment_status === 'paid' ? 'paid' : 'sent',
                'created_by' => auth()->id(),
            ]);

            // Create line items
            foreach ($request->items as $index => $item) {
                if (!empty($item['description']) && !empty($item['rate'])) {
                    $invoice->items()->create([
                        'description' => $item['description'],
                        'quantity' => $item['quantity'] ?? 1,
                        'rate' => $item['rate'],
                        'amount' => $item['amount'] ?? ($item['quantity'] * $item['rate']),
                        'order' => $index,
                    ]);
                }
            }
        }

        return redirect()->route('shipments.show', $shipment)
            ->with('success', 'Shipment created successfully. Tracking Number: ' . $shipment->tracking_number);
    }


    /**
     * Display the specified resource.
     */
    public function show(Shipment $shipment)
    {
        $shipment->load(['client', 'statusUpdates', 'invoices.items']);
        return view('shipments.show', compact('shipment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Shipment $shipment)
    {
        $shipment->load(['invoices.items']);
        $clients = Client::orderBy('name')->get();
        return view('shipments.edit', compact('shipment', 'clients'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateShipmentRequest $request, Shipment $shipment)
    {
        $shipment->update($request->validated());

        return redirect()->route('shipments.show', $shipment)
            ->with('success', 'Shipment updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shipment $shipment)
    {
        $shipment->delete();

        return redirect()->route('shipments.index')
            ->with('success', 'Shipment deleted successfully.');
    }
    public function label(Shipment $shipment)
    {
        return view('shipments.label', compact('shipment'));
    }

    public function invoice(Shipment $shipment)
    {
        // Load relationships
        $shipment->load(['client', 'receiver', 'invoices.items']);
        
        // Get or create invoice for this shipment
        $invoice = $shipment->invoices()->first();
        
        if (!$invoice) {
            // Create new invoice from shipment data
            $invoice = \App\Models\Invoice::create([
                'shipment_id' => $shipment->id,
                'invoice_number' => \App\Models\Invoice::generateInvoiceNumber(),
                'issue_date' => now(),
                'due_date' => now()->addDays(30),
                'subtotal' => 0,
                'tax' => $shipment->tax ?? 0,
                'discount' => $shipment->discount ?? 0,
                'total' => 0,
                'status' => $shipment->payment_status === 'paid' ? 'paid' : 'sent',
                'created_by' => auth()->id(),
            ]);
            
            // Create line items
            $order = 0;
            $subtotal = 0;
            
            // Add shipping cost as line item
            if ($shipment->shipping_cost > 0) {
                $amount = $shipment->shipping_cost;
                $invoice->items()->create([
                    'description' => ucfirst($shipment->service_type ?? 'Standard') . ' Shipping Service',
                    'quantity' => 1,
                    'rate' => $amount,
                    'amount' => $amount,
                    'order' => $order++,
                ]);
                $subtotal += $amount;
            }
            
            // Add insurance as line item
            if ($shipment->insurance_value > 0) {
                $amount = $shipment->insurance_value;
                $invoice->items()->create([
                    'description' => 'Insurance Coverage',
                    'quantity' => 1,
                    'rate' => $amount,
                    'amount' => $amount,
                    'order' => $order++,
                ]);
                $subtotal += $amount;
            }
            
            // Update invoice totals
            $invoice->update([
                'subtotal' => $subtotal,
                'total' => $subtotal + $invoice->tax - $invoice->discount,
            ]);
        }
        
        // Get company settings
        $companySettings = [
            'name' => 'Bryan Logistics',
            'address' => 'Ttowa Mall building, Room C102, Opposite CPS Kampala',
            'phone' => '0755 729 943 / 0743 507 702',
            'email' => 'bryanlogistics256@gmail.com',
            'logo' => 'images/logo.png',
        ];
        
        return view('shipments.invoice', compact('shipment', 'invoice', 'companySettings'));
    }
}

