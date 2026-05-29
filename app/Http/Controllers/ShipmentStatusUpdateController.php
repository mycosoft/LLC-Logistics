<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\ShipmentStatusUpdate;
use App\Events\ShipmentStatusUpdatedEvent;
use Illuminate\Http\Request;

class ShipmentStatusUpdateController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'shipment_id' => 'required|exists:shipments,id',
            'status' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        // Prevent Picked Up status if invoice is not fully paid
        if (strtolower(trim($validated['status'])) === 'picked up') {
            $shipmentCheck = Shipment::with(['invoices.items', 'invoices.payments'])->findOrFail($validated['shipment_id']);
            $invoice = $shipmentCheck->invoices->first();

            if ($invoice && $invoice->balance > 0) {
                return redirect()->back()
                    ->with('error', 'Cannot change status to "Picked Up". The invoice has an outstanding balance. Please ensure the invoice is fully paid first.');
            }
        }

        // Create the status update
        $statusUpdate = ShipmentStatusUpdate::create($validated);

        // Update the shipment's current status
        $shipment = Shipment::findOrFail($validated['shipment_id']);
        $shipment->update(['current_status' => $validated['status']]);

        // Track when package becomes ready for pickup
        if (strtolower(trim($validated['status'])) === 'ready for pickup') {
            $shipment->update(['ready_for_pickup_at' => now(), 'auction_notified_at' => null]);
        }

        // Clear auction flags if status changes away from Ready for Pickup
        if (strtolower(trim($validated['status'])) !== 'ready for pickup' && $shipment->ready_for_pickup_at) {
            $shipment->update(['ready_for_pickup_at' => null, 'auction_notified_at' => null]);
        }

        // Load the client relationship
        $shipment->load('client');

        // Dispatch the event for notifications
        event(new ShipmentStatusUpdatedEvent($shipment, $statusUpdate));

        return redirect()->route('admin.shipments.show', $shipment)
            ->with('success', 'Status update added successfully.');
    }
}
