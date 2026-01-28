<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Notifications\Channels\WhatsAppChannel;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceSent extends Notification implements ShouldQueue
{
    use Queueable;

    public $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function via($notifiable)
    {
        $channels = [];
        
        if (\App\Models\Setting::get('notify_status_change_email', 1) && $notifiable->email) {
            $channels[] = 'mail';
        }
        
        if (\App\Models\Setting::get('notify_status_change_whatsapp', 1) && $notifiable->phone) {
            $channels[] = WhatsAppChannel::class;
        }
        
        return $channels;
    }

    public function toMail($notifiable)
    {
        $companySettings = [
            'name' => 'Bryanz Logistics',
            'address' => 'Ttowa Mall building, Room C102, Opposite CPS Kampala',
            'phone' => '0755 729 943 / 0743 507 702',
            'email' => 'bryanlogistics256@gmail.com',
            'logo' => 'images/logo.png',
        ];

        $pdf = Pdf::loadView('invoices.pdf', [
            'invoice' => $this->invoice,
            'shipment' => $this->invoice->shipment,
            'companySettings' => $companySettings
        ]);

        $dueDate = $this->invoice->due_date ? $this->invoice->due_date->format('F d, Y') : 'Upon receipt';

        return (new MailMessage)
            ->subject('Invoice - ' . $this->invoice->invoice_number)
            ->greeting('Dear ' . $notifiable->name . ',')
            ->line('Please find attached your invoice.')
            ->line('Invoice Number: ' . $this->invoice->invoice_number)
            ->line('Total Amount: UGX ' . number_format($this->invoice->total, 2))
            ->line('Amount Paid: UGX ' . number_format($this->invoice->amount_paid, 2))
            ->line('Balance Due: UGX ' . number_format($this->invoice->balance, 2))
            ->line('Due Date: ' . $dueDate)
            ->attachData($pdf->output(), 'invoice-' . $this->invoice->invoice_number . '.pdf', [
                'mime' => 'application/pdf',
            ])
            ->line('Please make payment at your earliest convenience.')
            ->line('Thank you for your business!');
    }

    public function toWhatsApp($notifiable)
    {
        $dueDate = $this->invoice->due_date ? $this->invoice->due_date->format('F d, Y') : 'Upon receipt';
        
        $message = "📄 *Invoice*\n\n";
        $message .= "Dear {$notifiable->name},\n\n";
        $message .= "📋 *Invoice Details:*\n";
        $message .= "Invoice No: {$this->invoice->invoice_number}\n";
        $message .= "Total: UGX " . number_format($this->invoice->total, 2) . "\n";
        $message .= "Paid: UGX " . number_format($this->invoice->amount_paid, 2) . "\n";
        $message .= "Balance: UGX " . number_format($this->invoice->balance, 2) . "\n";
        $message .= "Due Date: {$dueDate}\n\n";
        
        if ($this->invoice->shipment) {
            $message .= "Tracking: {$this->invoice->shipment->tracking_number}\n\n";
        }
        
        $message .= "Please make payment at your earliest convenience.\n\n";
        $message .= "Thank you for your business!\n";
        $message .= "📞 Support: 0755 729 943";

        return $message;
    }
}
