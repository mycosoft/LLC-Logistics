@extends('adminlte::page')

@section('title', 'Batch Details')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Batch: {{ $batch->batch_number }}</h1>
        </div>
        <div class="col-sm-6">
            <a href="{{ route('admin.batches.packing-list', $batch) }}" class="btn btn-success float-right ml-2">
                <i class="fas fa-file-pdf"></i> Generate Packing List
            </a>
            <a href="{{ route('admin.batches.edit', $batch) }}" class="btn btn-warning float-right ml-2">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('admin.batches.index') }}" class="btn btn-secondary float-right">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('success') }}
        </div>
    @endif

    <div class="row">
        <!-- Batch Information -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Batch Information</h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-5">Batch Number:</dt>
                        <dd class="col-sm-7"><strong>{{ $batch->batch_number }}</strong></dd>

                        <dt class="col-sm-5">Name:</dt>
                        <dd class="col-sm-7">{{ $batch->name }}</dd>

                        <dt class="col-sm-5">Status:</dt>
                        <dd class="col-sm-7">
                            @php
                                $statusColors = [
                                    'pending' => 'warning',
                                    'processing' => 'info',
                                    'in_transit' => 'primary',
                                    'delivered' => 'success',
                                    'cancelled' => 'danger'
                                ];
                                $badgeClass = $statusColors[$batch->current_status] ?? 'secondary';
                            @endphp
                            <span class="badge badge-{{ $badgeClass }}">{{ ucfirst(str_replace('_', ' ', $batch->current_status)) }}</span>
                        </dd>

                        <dt class="col-sm-5">Shipments:</dt>
                        <dd class="col-sm-7"><span class="badge badge-info">{{ $batch->shipments->count() }}</span></dd>

                        <dt class="col-sm-5">Created By:</dt>
                        <dd class="col-sm-7">{{ $batch->creator->name ?? 'N/A' }}</dd>

                        <dt class="col-sm-5">Created:</dt>
                        <dd class="col-sm-7">{{ $batch->created_at->format('M d, Y H:i') }}</dd>
                    </dl>

                    @if($batch->description)
                        <hr>
                        <p><strong>Description:</strong></p>
                        <p>{{ $batch->description }}</p>
                    @endif
                </div>
            </div>

            <!-- Update Batch Status -->
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">Update Batch Status</h3>
                </div>
                <form action="{{ route('admin.batches.update-status', $batch) }}" method="POST" onsubmit="return confirm('This will update the status of ALL {{ $batch->shipments->count() }} shipments in this batch. Continue?');">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="current_status">New Status</label>
                            <select name="current_status" id="current_status" class="form-control" required>
                                <option value="Pending" {{ $batch->current_status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Picked Up" {{ $batch->current_status == 'Picked Up' ? 'selected' : '' }}>Picked Up</option>
                                <option value="In Transit" {{ $batch->current_status == 'In Transit' ? 'selected' : '' }}>In Transit</option>
                                <option value="Arrived at Facility" {{ $batch->current_status == 'Arrived at Facility' ? 'selected' : '' }}>Arrived at Facility</option>
                                <option value="Out for Delivery" {{ $batch->current_status == 'Out for Delivery' ? 'selected' : '' }}>Out for Delivery</option>
                                <option value="Delivered" {{ $batch->current_status == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="On Hold" {{ $batch->current_status == 'On Hold' ? 'selected' : '' }}>On Hold</option>
                                <option value="Cancelled" {{ $batch->current_status == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="location">Location <span class="text-danger">*</span></label>
                            <input type="text" name="location" id="location" class="form-control" placeholder="Current location (e.g., Warehouse, In Transit, etc.)" required>
                            <small class="form-text text-muted">Specify the current location for this status update</small>
                        </div>
                        <div class="form-group">
                            <label for="notes">Notes (Optional)</label>
                            <textarea name="notes" id="notes" rows="3" class="form-control" placeholder="Add notes about this status update..."></textarea>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-warning btn-block">
                            <i class="fas fa-sync"></i> Update Status for All Shipments
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Shipments in Batch -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Shipments in This Batch</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addShipmentModal">
                            <i class="fas fa-plus"></i> Add Shipment
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tracking #</th>
                                    <th>Client</th>
                                    <th>Route</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($batch->shipments as $shipment)
                                    <tr>
                                        <td><strong>{{ $shipment->tracking_number }}</strong></td>
                                        <td>{{ $shipment->client->name }}</td>
                                        <td>{{ $shipment->origin }} → {{ $shipment->destination }}</td>
                                        <td><span class="badge badge-secondary">{{ $shipment->current_status }}</span></td>
                                        <td>
                                            <a href="{{ route('admin.shipments.show', $shipment) }}" class="btn btn-sm btn-info" title="View Shipment">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form action="{{ route('admin.batches.remove-shipment', [$batch, $shipment]) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Remove this shipment from the batch?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Remove from Batch">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No shipments in this batch yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Invoices & Payments -->
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-file-invoice-dollar"></i> Invoices & Payments</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Client</th>
                                    <th>Total</th>
                                    <th>Paid</th>
                                    <th>Balance</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalInvoiced = 0;
                                    $totalPaid = 0;
                                    $totalOutstanding = 0;
                                @endphp
                                @forelse($batch->shipments as $shipment)
                                    @if($shipment->invoice)
                                        @php
                                            $totalInvoiced += $shipment->invoice->total;
                                            $totalPaid += $shipment->invoice->amount_paid;
                                            $totalOutstanding += $shipment->invoice->balance;
                                        @endphp
                                        <tr>
                                            <td><strong>{{ $shipment->invoice->invoice_number }}</strong></td>
                                            <td>{{ $shipment->client->name }}</td>
                                            <td>UGX {{ number_format($shipment->invoice->total, 0) }}</td>
                                            <td class="text-success">UGX {{ number_format($shipment->invoice->amount_paid, 0) }}</td>
                                            <td class="text-danger"><strong>UGX {{ number_format($shipment->invoice->balance, 0) }}</strong></td>
                                            <td>
                                                @if($shipment->invoice->status == 'paid')
                                                    <span class="badge badge-success">Paid</span>
                                                @elseif($shipment->invoice->status == 'partially_paid')
                                                    <span class="badge badge-warning">Partially Paid</span>
                                                @elseif($shipment->invoice->status == 'overdue')
                                                    <span class="badge badge-danger">Overdue</span>
                                                @else
                                                    <span class="badge badge-secondary">{{ ucfirst($shipment->invoice->status) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.invoices.show', $shipment->invoice->id) }}" class="btn btn-sm btn-info" title="View Invoice">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($shipment->invoice->status != 'paid')
                                                    <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#paymentModal{{ $shipment->invoice->id }}" title="Record Payment">
                                                        <i class="fas fa-money-bill"></i>
                                                    </button>
                                                @endif
                                                <a href="{{ route('admin.invoices.pdf', $shipment->invoice->id) }}" class="btn btn-sm btn-danger" title="Download PDF" target="_blank">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                            </td>
                                        </tr>

                                        <!-- Payment Modal for this invoice -->
                                        <div class="modal fade" id="paymentModal{{ $shipment->invoice->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-success">
                                                        <h5 class="modal-title">Record Payment - {{ $shipment->invoice->invoice_number }}</h5>
                                                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                                    </div>
                                                    <form action="{{ route('admin.payments.store', $shipment->invoice->id) }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="invoice_id" value="{{ $shipment->invoice->id }}">
                                                        <div class="modal-body">
                                                            <div class="alert alert-info">
                                                                <strong>Balance Due:</strong> UGX {{ number_format($shipment->invoice->balance, 0) }}
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Amount <span class="text-danger">*</span></label>
                                                                <input type="number" step="0.01" name="amount" class="form-control" max="{{ $shipment->invoice->balance }}" required>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Payment Date <span class="text-danger">*</span></label>
                                                                <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Payment Method <span class="text-danger">*</span></label>
                                                                <select name="payment_method" class="form-control" required>
                                                                    <option value="cash">Cash</option>
                                                                    <option value="card">Card</option>
                                                                    <option value="bank_transfer">Bank Transfer</option>
                                                                    <option value="mobile_money">Mobile Money</option>
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Reference Number</label>
                                                                <input type="text" name="reference_number" class="form-control">
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Notes</label>
                                                                <textarea name="notes" class="form-control" rows="2"></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-success">
                                                                <i class="fas fa-check"></i> Record Payment
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No invoices found for this batch</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($totalInvoiced > 0)
                                <tfoot class="bg-light">
                                    <tr>
                                        <th colspan="2" class="text-right">TOTALS:</th>
                                        <th>UGX {{ number_format($totalInvoiced, 0) }}</th>
                                        <th class="text-success">UGX {{ number_format($totalPaid, 0) }}</th>
                                        <th class="text-danger">UGX {{ number_format($totalOutstanding, 0) }}</th>
                                        <th colspan="2"></th>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Shipment Modal -->
    <div class="modal fade" id="addShipmentModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Shipment to Batch</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.batches.add-shipment', $batch) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="shipment_id">Select Shipment</label>
                            <select name="shipment_id" id="shipment_id" class="form-control" required>
                                <option value="">-- Select a shipment --</option>
                                @foreach($availableShipments as $shipment)
                                    <option value="{{ $shipment->id }}">
                                        {{ $shipment->tracking_number }} - {{ $shipment->client->name }} ({{ $shipment->origin }} → {{ $shipment->destination }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @if($availableShipments->isEmpty())
                            <div class="alert alert-info">
                                No available shipments. All shipments are already in batches.
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" {{ $availableShipments->isEmpty() ? 'disabled' : '' }}>
                            <i class="fas fa-plus"></i> Add to Batch
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('footer')
    <strong>Copyright &copy; {{ date('Y') }} <a href="#">Bryanz Logistics</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
        <b>Support Call</b> 0750501151
    </div>
@stop
