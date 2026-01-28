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
            'location' => 'required|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        // Create the status update
        $statusUpdate = ShipmentStatusUpdate::create($validated);

        // Update the shipment's current status
        $shipment = Shipment::findOrFail($validated['shipment_id']);
        $shipment->update(['current_status' => $validated['status']]);

        // Load the client relationship
        $shipment->load('client');

        // Dispatch the event for notifications
        event(new ShipmentStatusUpdatedEvent($shipment, $statusUpdate));

        return redirect()->route('admin.shipments.show', $shipment)
            ->with('success', 'Status update added successfully.');
    }
}
