<?php

namespace App\Jobs;

use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendBulkNotificationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $subject;
    protected $message;
    protected $channel;
    protected $clientIds;

    /**
     * Create a new job instance.
     */
    public function __construct($subject, $message, $channel, $clientIds = null)
    {
        $this->subject = $subject;
        $this->message = $message;
        $this->channel = $channel;
        $this->clientIds = $clientIds;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $query = Client::query();
        
        // Filter by specific clients if provided
        if ($this->clientIds) {
            $query->whereIn('id', $this->clientIds);
        }
        
        $clients = $query->get();

        foreach ($clients as $client) {
            if ($this->channel === 'email' && $client->email) {
                try {
                    // In a real app, you'd create a Mailable class for this
                    // For now, we'll log it to simulate sending
                    Log::info("Sending Bulk Email to {$client->email}: Subject: {$this->subject}");
                    
                    // Mail::raw($this->message, function ($msg) use ($client) {
                    //     $msg->to($client->email)->subject($this->subject);
                    // });

                } catch (\Exception $e) {
                    Log::error("Failed to send bulk email to {$client->email}: " . $e->getMessage());
                }
            } elseif ($this->channel === 'sms' && $client->phone) {
                // Placeholder for SMS logic
                Log::info("Sending Bulk SMS to {$client->phone}: {$this->message}");
            } elseif ($this->channel === 'whatsapp' && $client->phone) {
                try {
                    // Send WhatsApp notification
                    $client->notify(new \App\Notifications\WhatsAppMessage($this->subject, $this->message));
                    Log::info("Sending Bulk WhatsApp to {$client->phone}: {$this->message}");
                } catch (\Exception $e) {
                    Log::error("Failed to send bulk WhatsApp to {$client->phone}: " . $e->getMessage());
                }
            }
        }
    }
}
