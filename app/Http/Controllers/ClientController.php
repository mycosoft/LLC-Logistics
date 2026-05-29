<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Client::query();

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%");
            });
        }

        $clients = $query->latest()->paginate(15);

        return view('clients.index', compact('clients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('clients.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClientRequest $request)
    {
        Client::create($request->validated());

        return redirect()->route('clients.index')
            ->with('success', 'Client created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        $client->load([
            'shipments.invoices.payments',
            'shipments.invoices.items',
        ]);

        // Build financial summary
        $totalInvoiced   = 0;
        $totalPaid       = 0;
        $totalOutstanding = 0;
        foreach ($client->shipments as $shipment) {
            if ($shipment->invoice) {
                $totalInvoiced   += $shipment->invoice->total;
                $totalPaid       += $shipment->invoice->amount_paid;
                $totalOutstanding += $shipment->invoice->balance;
            }
        }

        return view('clients.show', compact('client', 'totalInvoiced', 'totalPaid', 'totalOutstanding'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClientRequest $request, Client $client)
    {
        $client->update($request->validated());

        return redirect()->route('clients.index')
            ->with('success', 'Client updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'Client deleted successfully.');
    }

    /**
     * Quick store a client via AJAX for modals on cargo create pages.
     */
    public function quickStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|unique:clients,email',
            'company' => 'nullable|string|max:255',
            'address' => 'nullable|string',
        ]);

        $client = Client::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Client "' . $client->name . '" added successfully.',
            'client' => [
                'id' => $client->id,
                'name' => $client->name,
                'phone' => $client->phone,
                'company' => $client->company,
            ]
        ]);
    }

    /**
     * Search clients for autocomplete
     */
    public function search(Request $request)
    {
        $search = $request->get('q', '');
        
        $clients = Client::where('name', 'like', "%{$search}%")
            ->orWhere('phone', 'like', "%{$search}%")
            ->orWhere('company', 'like', "%{$search}%")
            ->limit(20)
            ->get(['id', 'name', 'phone', 'company', 'email']);
        
        return response()->json($clients);
    }
}
