<?php

namespace App\Listeners;

use App\Events\ShipmentStatusUpdatedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use App\Services\NotificationService;

class NotifyClientListener
{
    protected $notificationService;

    /**
     * Create the event listener.
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the event.
     */
    public function handle(ShipmentStatusUpdatedEvent $event): void
    {
        Log::info("Processing notification for Tracking #{$event->shipment->tracking_number}");
        
        if ($event->shipment->client) {
            try {
                $event->shipment->client->notify(new \App\Notifications\ShipmentStatusChanged($event->shipment, $event->statusUpdate));
            } catch (\Exception $e) {
                Log::error("Failed to send notification for Tracking #{$event->shipment->tracking_number}: " . $e->getMessage());
            }
        }
    }
}
