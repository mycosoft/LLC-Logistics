<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices.
     */
    public function index(Request $request)
    {
        $query = Invoice::with(['shipment.client', 'payments']);

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from != '') {
            $query->whereDate('issue_date', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to != '') {
            $query->whereDate('issue_date', '<=', $request->date_to);
        }

        // Search by invoice number or tracking number
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('shipment', function ($sq) use ($search) {
                    $sq->where('tracking_number', 'like', "%{$search}%");
                }
                );
            });
        }

        $invoices = $query->latest('issue_date')->paginate(15);

        return view('invoices.index', compact('invoices'));
    }

    /**
     * Display specified invoice.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['shipment.client', 'shipment.receiver', 'payments.recorder', 'items']);

        $companySettings = [
            'name' => 'LLC Express Logistics',
            'address' => 'Kawempe - Tula',
            'phone' => '+256 703 948463',
            'email' => 'info@llclogistics.com',
            'logo' => 'images/logo.png',
        ];

        return view('invoices.show', compact('invoice', 'companySettings'));
    }

    /**
     * Generate PDF for specified invoice.
     */
    public function generatePDF(Invoice $invoice)
    {
        $invoice->load(['shipment.client', 'shipment.receiver', 'items']);

        $companySettings = [
            'name' => 'LLC Express Logistics',
            'address' => 'Kawempe - Tula',
            'phone' => '+256 703 948463',
            'email' => 'info@llclogistics.com',
            'logo' => 'images/logo.png',
        ];

        $pdf = Pdf::loadView('invoices.pdf', [
            'invoice' => $invoice,
            'shipment' => $invoice->shipment,
            'companySettings' => $companySettings
        ]);

        return $pdf->download('invoice-' . $invoice->invoice_number . '.pdf');
    }

    /**
     * Send invoice to client via WhatsApp/Email.
     */
    public function sendInvoice(Request $request, Invoice $invoice)
    {
        $invoice->load(['shipment.client']);

        $client = $invoice->shipment->client;

        if (!$client) {
            return redirect()->back()->with('error', 'Client not found for this invoice.');
        }

        // Check notification preferences
        $notifyEmail = \App\Models\Setting::get('notify_status_change_email', 1);
        $notifyWhatsapp = \App\Models\Setting::get('notify_status_change_whatsapp', 1);

        $sentVia = [];

        // Send via Email
        if ($notifyEmail && $client->email) {
            try {
                $client->notify(new \App\Notifications\InvoiceSent($invoice));
                $sentVia[] = 'email';
            }
            catch (\Exception $e) {
                \Log::error('Failed to send invoice email: ' . $e->getMessage());
            }
        }

        // Send via WhatsApp
        if ($notifyWhatsapp && $client->phone) {
            try {
                $client->notify(new \App\Notifications\InvoiceSent($invoice));
                $sentVia[] = 'whatsapp';
            }
            catch (\Exception $e) {
                \Log::error('Failed to send invoice WhatsApp: ' . $e->getMessage());
            }
        }

        if (empty($sentVia)) {
            return redirect()->back()->with('error', 'No notification channels available or enabled.');
        }

        // Update invoice with delivery tracking
        $invoice->update([
            'sent_at' => now(),
            'sent_via' => implode(',', $sentVia),
        ]);

        $message = 'Invoice sent successfully via ' . implode(' and ', $sentVia) . '.';
        return redirect()->back()->with('success', $message);
    }
}
