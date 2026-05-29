<?php

namespace App\Console\Commands;

use App\Models\Shipment;
use App\Models\ShipmentStatusUpdate;
use Illuminate\Console\Command;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;

class CheckAuctionShipments extends Command
{
    protected $signature = 'shipments:check-auction';

    protected $description = 'Check for shipments stuck at Ready for Pickup over 14 days and send auction warnings';

    public function handle()
    {
        $cutoff = now()->subDays(14);

        $shipments = Shipment::with('client')
            ->where('current_status', 'Ready for Pickup')
            ->whereNotNull('ready_for_pickup_at')
            ->where('ready_for_pickup_at', '<=', $cutoff)
            ->whereNull('auction_notified_at')
            ->get();

        $count = $shipments->count();
        $this->info("Found {$count} uncollected shipment(s) older than 14 days.");

        $whatsappService = app(WhatsAppService::class);

        foreach ($shipments as $shipment) {
            $this->line(" - {$shipment->tracking_number} (Client: " . ($shipment->client->name ?? 'N/A') . ")");

            // Log the status update
            $shipment->statusUpdates()->create([
                'status' => 'Auction Warning',
                'location' => 'System',
                'remarks' => 'Package uncollected for 14+ days. Auction warning sent.',
            ]);

            if ($shipment->client && $shipment->client->phone) {
                $message = "*AUCTION WARNING*\n\n";
                $message .= "Dear {$shipment->client->name},\n\n";
                $message .= "Your package has been ready for pickup for over 14 days.\n\n";
                $message .= "📦 Tracking: {$shipment->tracking_number}\n";
                $message .= "📅 Ready since: {$shipment->ready_for_pickup_at->format('F d, Y')}\n\n";
                $message .= "⚠️ If not collected within 3 days, your package will be moved to auction.\n\n";
                $message .= "Please pick up immediately!\n";
                $message .= "📍 Location: LLC Express Logistics, Kawempe - Tula\n";
                $message .= "📞 Call: +256 703 948463";

                try {
                    $result = $whatsappService->sendMessage($shipment->client->phone, $message);
                    if ($result['success']) {
                        $this->info("   WhatsApp sent to {$shipment->client->phone}");
                    }
                } catch (\Exception $e) {
                    Log::error("[Auction Check] WhatsApp failed for {$shipment->tracking_number}: " . $e->getMessage());
                    $this->error("   WhatsApp failed: " . $e->getMessage());
                }
            }

            $shipment->update(['auction_notified_at' => now()]);
        }

        $this->info("Auction check completed. {$count} shipment(s) processed.");

        return 0;
    }
}
