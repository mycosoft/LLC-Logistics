<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Reports dashboard
     */
    public function index()
    {
        return view('reports.index');
    }

    /**
     * Revenue report
     */
    public function revenue(Request $request)
    {
        $query = Payment::with(['invoice.shipment.client']);

        // Date filters
        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        // Payment method filter
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        $payments = $query->latest('payment_date')->paginate(50);
        $totalRevenue = $query->sum('amount');

        return view('reports.revenue', compact('payments', 'totalRevenue'));
    }

    /**
     * Outstanding invoices report
     */
    public function outstanding(Request $request)
    {
        $query = Invoice::with(['shipment.client', 'payments'])
            ->where('status', '!=', 'paid')
            ->where('status', '!=', 'cancelled');

        // Date filters
        if ($request->filled('date_from')) {
            $query->whereDate('issue_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('issue_date', '<=', $request->date_to);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $invoices = $query->latest('issue_date')->paginate(50);
        $totalOutstanding = $invoices->sum('balance');

        return view('reports.outstanding', compact('invoices', 'totalOutstanding'));
    }

    /**
     * Payment history report
     */
    public function payments(Request $request)
    {
        return $this->revenue($request);
    }

    /**
     * Shipments report
     */
    public function shipments(Request $request)
    {
        $query = \App\Models\Shipment::with(['client', 'batch']);

        // Date filters
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('current_status', $request->status);
        }

        // Type filter
        if ($request->filled('shipment_type')) {
            $query->where('shipment_type', $request->shipment_type);
        }

        // Client filter
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        $shipments = $query->latest()->paginate(50);

        // Calculate statistics
        $stats = [
            'total' => \App\Models\Shipment::count(),
            'pending' => \App\Models\Shipment::where('current_status', 'Pending')->count(),
            'in_transit' => \App\Models\Shipment::where('current_status', 'In Transit')->count(),
            'delivered' => \App\Models\Shipment::where('current_status', 'Delivered')->count(),
        ];

        // Get all clients for filter dropdown
        $clients = \App\Models\Client::orderBy('name')->get();

        return view('reports.shipments', compact('shipments', 'stats', 'clients'));
    }

    /**
     * Clients report
     */
    public function clients(Request $request)
    {
        $query = \App\Models\Client::withCount('shipments');

        // Search filter
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        $clients = $query->latest()->paginate(50);

        return view('reports.clients', compact('clients'));
    }

    /**
     * Analytics dashboard
     */
    public function analytics()
    {
        $data = [
            'totalShipments' => \App\Models\Shipment::count(),
            'totalClients' => \App\Models\Client::count(),
            'totalRevenue' => \App\Models\Payment::sum('amount'),
            'monthlyRevenue' => \App\Models\Payment::whereYear('payment_date', now()->year)
                ->whereMonth('payment_date', now()->month)
                ->sum('amount'),
            'shipmentsByStatus' => \App\Models\Shipment::select('current_status', \DB::raw('count(*) as total'))
                ->groupBy('current_status')
                ->get(),
            'shipmentsByType' => \App\Models\Shipment::select('shipment_type', \DB::raw('count(*) as total'))
                ->groupBy('shipment_type')
                ->get(),
        ];

        return view('reports.analytics', $data);
    }

    /**
     * Export shipments to PDF
     */
    public function exportShipmentsPdf(Request $request)
    {
        $shipments = \App\Models\Shipment::with(['client', 'batch'])->latest()->get();
        $pdf = Pdf::loadView('reports.shipments-pdf', compact('shipments'));
        return $pdf->download('shipments-report-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Export clients to PDF
     */
    public function exportClientsPdf(Request $request)
    {
        $clients = \App\Models\Client::withCount('shipments')->latest()->get();
        $pdf = Pdf::loadView('reports.clients-pdf', compact('clients'));
        return $pdf->download('clients-report-' . date('Y-m-d') . '.pdf');
    }
}
