@extends('adminlte::page')

@section('title', 'Client Details - ' . $client->name)

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Client: {{ $client->name }}</h1>
        </div>
        <div class="col-sm-6">
            <a href="{{ route('clients.edit', $client) }}" class="btn btn-warning float-right">
                <i class="fas fa-edit"></i> Edit Client
            </a>
        </div>
    </div>
@stop

@section('content')
    {{-- Financial Summary Cards --}}
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ number_format($totalInvoiced, 2) }}</h3>
                    <p>Total Invoiced</p>
                </div>
                <div class="icon">
                    <i class="fas fa-file-invoice"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ number_format($totalPaid, 2) }}</h3>
                    <p>Total Paid</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ number_format($totalOutstanding, 2) }}</h3>
                    <p>Outstanding</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $client->shipments->count() }}</h3>
                    <p>Total Shipments</p>
                </div>
                <div class="icon">
                    <i class="fas fa-box"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Client Information --}}
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Client Information</h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-2">Name:</dt>
                        <dd class="col-sm-4">{{ $client->name }}</dd>

                        <dt class="col-sm-2">Email:</dt>
                        <dd class="col-sm-4">{{ $client->email }}</dd>

                        <dt class="col-sm-2">Phone:</dt>
                        <dd class="col-sm-4">{{ $client->phone }}</dd>

                        <dt class="col-sm-2">Company:</dt>
                        <dd class="col-sm-4">{{ $client->company ?? 'N/A' }}</dd>

                        <dt class="col-sm-2">Address:</dt>
                        <dd class="col-sm-4">{{ $client->address ?? 'N/A' }}</dd>

                        <dt class="col-sm-2">Created:</dt>
                        <dd class="col-sm-4">{{ $client->created_at->format('M d, Y H:i') }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs for Shipments, Invoices, Payments --}}
    <div class="card">
        <div class="card-header p-2">
            <ul class="nav nav-pills" id="clientDetailsTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="shipments-tab" data-toggle="pill" href="#shipments" role="tab" aria-controls="shipments" aria-selected="true">
                        <i class="fas fa-box"></i> Shipments ({{ $client->shipments->count() }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="invoices-tab" data-toggle="pill" href="#invoices" role="tab" aria-controls="invoices" aria-selected="false">
                        <i class="fas fa-file-invoice"></i> Invoices ({{ $client->shipments->flatMap->invoices->count() }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="payments-tab" data-toggle="pill" href="#payments" role="tab" aria-controls="payments" aria-selected="false">
                        <i class="fas fa-credit-card"></i> Payments
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="clientDetailsTabsContent">
                {{-- Shipments Tab --}}
                <div class="tab-pane fade show active" id="shipments" role="tabpanel" aria-labelledby="shipments-tab">
                    @if($client->shipments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Tracking #</th>
                                        <th>Type</th>
                                        <th>Origin</th>
                                        <th>Destination</th>
                                        <th>Weight (kg)</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($client->shipments as $shipment)
                                        <tr>
                                            <td><strong>{{ $shipment->tracking_number }}</strong></td>
                                            <td>
                                                @if($shipment->shipment_type === 'air')
                                                    <span class="badge badge-primary"><i class="fas fa-plane"></i> Air</span>
                                                @else
                                                    <span class="badge badge-info"><i class="fas fa-ship"></i> Sea</span>
                                                @endif
                                            </td>
                                            <td>{{ $shipment->origin }}</td>
                                            <td>{{ $shipment->destination }}</td>
                                            <td>{{ number_format($shipment->weight ?? 0, 2) }}</td>
                                            <td>
                                                @switch($shipment->current_status)
                                                    @case('Pending')
                                                        <span class="badge badge-secondary">{{ $shipment->current_status }}</span>
                                                        @break
                                                    @case('Picked Up')
                                                        <span class="badge badge-info">{{ $shipment->current_status }}</span>
                                                        @break
                                                    @case('In Transit')
                                                        <span class="badge badge-primary">{{ $shipment->current_status }}</span>
                                                        @break
                                                    @case('Delivered')
                                                        <span class="badge badge-success">{{ $shipment->current_status }}</span>
                                                        @break
                                                    @default
                                                        <span class="badge badge-warning">{{ $shipment->current_status }}</span>
                                                @endswitch
                                            </td>
                                            <td>{{ $shipment->created_at->format('M d, Y') }}</td>
                                            <td>
                                                @if($shipment->shipment_type === 'air')
                                                    <a href="{{ route('admin.air-cargo.show', $shipment) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @else
                                                    <a href="{{ route('admin.sea-cargo.show', $shipment) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No shipments yet.</p>
                    @endif
                </div>

                {{-- Invoices Tab --}}
                <div class="tab-pane fade" id="invoices" role="tabpanel" aria-labelledby="invoices-tab">
                    @php
                        $allInvoices = $client->shipments->flatMap->invoices;
                    @endphp
                    @if($allInvoices->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Invoice #</th>
                                        <th>Shipment</th>
                                        <th>Issue Date</th>
                                        <th>Due Date</th>
                                        <th>Subtotal</th>
                                        <th>Tax</th>
                                        <th>Discount</th>
                                        <th>Total</th>
                                        <th>Paid</th>
                                        <th>Balance</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($allInvoices as $invoice)
                                        <tr>
                                            <td><strong>{{ $invoice->invoice_number }}</strong></td>
                                            <td>{{ $invoice->shipment->tracking_number ?? 'N/A' }}</td>
                                            <td>{{ $invoice->issue_date ? $invoice->issue_date->format('M d, Y') : 'N/A' }}</td>
                                            <td>{{ $invoice->due_date ? $invoice->due_date->format('M d, Y') : 'N/A' }}</td>
                                            <td>{{ number_format($invoice->subtotal ?? 0, 2) }}</td>
                                            <td>{{ number_format($invoice->tax ?? 0, 2) }}</td>
                                            <td>{{ number_format($invoice->discount ?? 0, 2) }}</td>
                                            <td><strong>{{ number_format($invoice->total ?? 0, 2) }}</strong></td>
                                            <td>{{ number_format($invoice->amount_paid ?? 0, 2) }}</td>
                                            <td>
                                                @if($invoice->balance > 0)
                                                    <span class="text-danger">{{ number_format($invoice->balance, 2) }}</span>
                                                @else
                                                    <span class="text-success">0.00</span>
                                                @endif
                                            </td>
                                            <td>
                                                @switch($invoice->status)
                                                    @case('draft')
                                                        <span class="badge badge-secondary">Draft</span>
                                                        @break
                                                    @case('sent')
                                                        <span class="badge badge-info">Sent</span>
                                                        @break
                                                    @case('paid')
                                                        <span class="badge badge-success">Paid</span>
                                                        @break
                                                    @case('partial')
                                                        <span class="badge badge-warning">Partial</span>
                                                        @break
                                                    @case('overdue')
                                                        <span class="badge badge-danger">Overdue</span>
                                                        @break
                                                    @case('cancelled')
                                                        <span class="badge badge-dark">Cancelled</span>
                                                        @break
                                                    @default
                                                        <span class="badge badge-secondary">{{ $invoice->status }}</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.invoices.show', $invoice) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.invoices.pdf', $invoice) }}" class="btn btn-sm btn-danger" target="_blank">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No invoices yet.</p>
                    @endif
                </div>

                {{-- Payments Tab --}}
                <div class="tab-pane fade" id="payments" role="tabpanel" aria-labelledby="payments-tab">
                    @php
                        $allPayments = $client->shipments->flatMap->invoices->flatMap->payments;
                    @endphp
                    @if($allPayments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Receipt #</th>
                                        <th>Invoice #</th>
                                        <th>Shipment</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Date</th>
                                        <th>Reference</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($allPayments as $payment)
                                        <tr>
                                            <td><strong>{{ $payment->receipt_number }}</strong></td>
                                            <td>{{ $payment->invoice->invoice_number ?? 'N/A' }}</td>
                                            <td>{{ $payment->invoice->shipment->tracking_number ?? 'N/A' }}</td>
                                            <td><strong>{{ number_format($payment->amount, 2) }}</strong></td>
                                            <td>
                                                @switch($payment->payment_method)
                                                    @case('cash')
                                                        <span class="badge badge-success"><i class="fas fa-money-bill"></i> Cash</span>
                                                        @break
                                                    @case('card')
                                                        <span class="badge badge-info"><i class="fas fa-credit-card"></i> Card</span>
                                                        @break
                                                    @case('bank_transfer')
                                                        <span class="badge badge-primary"><i class="fas fa-university"></i> Bank Transfer</span>
                                                        @break
                                                    @case('mobile_money')
                                                        <span class="badge badge-warning"><i class="fas fa-mobile-alt"></i> Mobile Money</span>
                                                        @break
                                                    @default
                                                        <span class="badge badge-secondary">{{ $payment->payment_method }}</span>
                                                @endswitch
                                            </td>
                                            <td>{{ $payment->payment_date ? $payment->payment_date->format('M d, Y') : $payment->created_at->format('M d, Y') }}</td>
                                            <td>{{ $payment->reference_number ?? 'N/A' }}</td>
                                            <td>
                                                <a href="{{ route('admin.payments.receipt', $payment) }}" class="btn btn-sm btn-info" target="_blank">
                                                    <i class="fas fa-receipt"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No payments yet.</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-footer">
            <a href="{{ route('clients.index') }}" class="btn btn-default">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>
@stop


@section('footer')
    <strong>Copyright &copy; {{ date('Y') }} <a href="#">LLC Express Logistics</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
        <b>Support Call</b> +256 703 948463
    </div>
@stop
