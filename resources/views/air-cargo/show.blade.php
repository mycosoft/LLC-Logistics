@extends('adminlte::page')

@section('title', 'Air Cargo Details')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Air Cargo Details: {{ $shipment->tracking_number }}</h1>
        </div>
        <div class="col-sm-6">
            <div class="float-right">
                <a href="{{ route('admin.air-cargo.edit', $shipment) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('admin.air-cargo.index') }}" class="btn btn-default">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
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
        <!-- Main Details Column -->
        <div class="col-md-8">
            <!-- Shipment Information Card -->
            <div class="card card-primary card-outline card-tabs">
                <div class="card-header p-0 pt-1 border-bottom-0">
                    <ul class="nav nav-tabs" id="shipment-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="details-tab" data-toggle="pill" href="#details" role="tab">Shipment Info</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="package-tab" data-toggle="pill" href="#package" role="tab">Package</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="parties-tab" data-toggle="pill" href="#parties" role="tab">Sender & Receiver</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="billing-tab" data-toggle="pill" href="#billing" role="tab">Billing</a>
                        </li>
                        @if($shipment->is_international)
                        <li class="nav-item">
                            <a class="nav-link" id="customs-tab" data-toggle="pill" href="#customs" role="tab">Customs</a>
                        </li>
                        @endif
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="shipment-tabContent">
                        
                        <!-- Basic Details Tab -->
                        <div class="tab-pane fade show active" id="details" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Client:</strong>
                                    <p><a href="{{ route('admin.clients.show', $shipment->client) }}">{{ $shipment->client->name }}</a></p>
                                    
                                    <strong>Origin:</strong>
                                    <p>{{ $shipment->origin }}</p>
                                    
                                    <strong>Destination:</strong>
                                    <p>{{ $shipment->destination }}</p>
                                    
                                    <strong>Service Type:</strong>
                                    <p>{{ ucfirst($shipment->service_type ?? 'Standard') }}</p>
                                </div>
                                <div class="col-md-6">
                                    <strong>Current Status:</strong>
                                    <p>
                                        @php
                                            $statusColors = [
                                                'Pending' => 'warning',
                                                'Picked Up' => 'info',
                                                'In Transit' => 'primary',
                                                'Arrived at Facility' => 'secondary',
                                                'Ready for Pickup' => 'success',
                                                'Out for Delivery' => 'info',
                                                'Delivered' => 'success',
                                                'On Hold' => 'dark',
                                                'Cancelled' => 'danger',
                                                'Auction Warning' => 'danger'
                                            ];
                                            $badgeClass = $statusColors[$shipment->current_status] ?? 'secondary';
                                        @endphp
                                        <span class="badge badge-{{ $badgeClass }}">{{ $shipment->current_status }}</span>
                                    </p>
                                    
                                    <strong>Shipment Type:</strong>
                                    <p>{{ ucfirst($shipment->shipment_type) }}</p>
                                    
                                    <strong>Expected Delivery:</strong>
                                    <p>{{ $shipment->expected_delivery_date ? $shipment->expected_delivery_date->format('M d, Y') : 'N/A' }}</p>
                                    
                                    <strong>Reference Number:</strong>
                                    <p>{{ $shipment->reference_number ?? 'N/A' }}</p>
                                </div>
                            </div>
                            
                            @if($shipment->description)
                                <hr>
                                <strong>Description:</strong>
                                <p>{{ $shipment->description }}</p>
                            @endif
                            
                            @if($shipment->delivery_instructions)
                                <hr>
                                <strong>Delivery Instructions:</strong>
                                <p class="text-info"><i class="fas fa-info-circle"></i> {{ $shipment->delivery_instructions }}</p>
                            @endif
                        </div>

                        <!-- Package Details Tab -->
                        <div class="tab-pane fade" id="package" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Number of Packages:</strong>
                                    <p>{{ $shipment->num_packages ?? 1 }}</p>
                                    
                                    <strong>Package Type:</strong>
                                    <p>{{ ucfirst($shipment->package_type ?? 'Box') }}</p>
                                    
                                    <strong>Weight:</strong>
                                    <p>{{ $shipment->weight }} kg</p>
                                </div>
                                <div class="col-md-6">
                                    <strong>Dimensions (W x H):</strong>
                                    <p>
                                        {{ $shipment->width ?? '-' }} x 
                                        {{ $shipment->height ?? '-' }} cm
                                    </p>
                                    
                                    <strong>Fragile:</strong>
                                    <p>
                                        @if($shipment->fragile)
                                            <span class="badge badge-danger"><i class="fas fa-wine-glass-alt"></i> Yes - Handle with Care</span>
                                        @else
                                            <span class="badge badge-success">No</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            
                            @if($shipment->special_instructions)
                                <hr>
                                <strong>Special Handling Instructions:</strong>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i> {{ $shipment->special_instructions }}
                                </div>
                            @endif
                        </div>

                        <!-- Sender & Receiver Tab -->
                        <div class="tab-pane fade" id="parties" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card card-light">
                                        <div class="card-header">
                                            <h3 class="card-title">Sender</h3>
                                        </div>
                                        <div class="card-body">
                                            <strong>Name:</strong> {{ $shipment->sender_name ?? 'N/A' }}<br>
                                            <strong>Phone:</strong> {{ $shipment->sender_phone ?? 'N/A' }}<br>
                                            <strong>Address:</strong><br>
                                            {{ $shipment->sender_address ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card card-light">
                                        <div class="card-header">
                                            <h3 class="card-title">Receiver</h3>
                                        </div>
                                        <div class="card-body">
                                            <strong>Name:</strong> {{ $shipment->receiver_name ?? 'N/A' }}<br>
                                            <strong>Phone:</strong> {{ $shipment->receiver_phone ?? 'N/A' }}<br>
                                            <strong>Address:</strong><br>
                                            {{ $shipment->receiver_address ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Billing Tab -->
                        <div class="tab-pane fade" id="billing" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Shipping Cost:</strong>
                                    <p class="lead">${{ number_format($shipment->shipping_cost, 2) }}</p>
                                </div>
                                <div class="col-md-6">
                                    <strong>Payment Method:</strong>
                                    <p>{{ ucfirst(str_replace('_', ' ', $shipment->payment_method ?? 'N/A')) }}</p>
                                    
                                    <strong>Payment Status:</strong>
                                    <p>
                                        @if($shipment->payment_status == 'paid')
                                            <span class="badge badge-success">Paid</span>
                                        @elseif($shipment->payment_status == 'refunded')
                                            <span class="badge badge-danger">Refunded</span>
                                        @else
                                            <span class="badge badge-warning">Pending</span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            @if($shipment->invoices->isNotEmpty() && $shipment->invoices->first()->items->isNotEmpty())
                                <hr>
                                <h5 class="mb-3">Invoice Line Items</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead class="bg-dark">
                                            <tr>
                                                <th>Description</th>
                                                <th class="text-right">Quantity</th>
                                                <th class="text-right">Rate</th>
                                                <th class="text-right">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($shipment->invoices->first()->items as $item)
                                            <tr>
                                                <td>{{ $item->description }}</td>
                                                <td class="text-right">{{ $item->quantity }}</td>
                                                <td class="text-right">{{ $shipment->currency ?? 'USD' }} {{ number_format($item->rate, 2) }}</td>
                                                <td class="text-right">{{ $shipment->currency ?? 'USD' }} {{ number_format($item->amount, 2) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr class="font-weight-bold">
                                                <td colspan="3" class="text-right">Subtotal:</td>
                                                <td class="text-right">{{ $shipment->currency ?? 'USD' }} {{ number_format($shipment->invoices->first()->subtotal, 2) }}</td>
                                            </tr>
                                            @if($shipment->invoices->first()->tax > 0)
                                            <tr>
                                                <td colspan="3" class="text-right">Tax:</td>
                                                <td class="text-right">{{ $shipment->currency ?? 'USD' }} {{ number_format($shipment->invoices->first()->tax, 2) }}</td>
                                            </tr>
                                            @endif
                                            @if($shipment->invoices->first()->discount > 0)
                                            <tr>
                                                <td colspan="3" class="text-right">Discount:</td>
                                                <td class="text-right">-{{ $shipment->currency ?? 'USD' }} {{ number_format($shipment->invoices->first()->discount, 2) }}</td>
                                            </tr>
                                            @endif
                                            <tr class="font-weight-bold bg-light">
                                                <td colspan="3" class="text-right">Total:</td>
                                                <td class="text-right">{{ $shipment->currency ?? 'USD' }} {{ number_format($shipment->invoices->first()->total, 2) }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @endif
                        </div>

                        <!-- Customs Tab -->
                        @if($shipment->is_international)
                        <div class="tab-pane fade" id="customs" role="tabpanel">
                            <div class="alert alert-info">
                                <i class="fas fa-globe"></i> This is an international shipment subject to customs regulations.
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Customs Value:</strong>
                                    <p>${{ number_format($shipment->customs_value, 2) }}</p>
                                </div>
                            </div>
                            
                            <strong>Customs Description:</strong>
                            <p>{{ $shipment->customs_description ?? 'N/A' }}</p>
                        </div>
                        @endif

                    </div>
                </div>
            </div>

            <!-- Status Timeline -->
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">Status Timeline</h3>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @foreach($shipment->statusUpdates as $update)
                            <div>
                                <i class="fas fa-truck bg-blue"></i>
                                <div class="timeline-item">
                                    <span class="time"><i class="fas fa-clock"></i> {{ $update->created_at->format('M d, H:i') }}</span>
                                    <h3 class="timeline-header"><strong>{{ $update->status }}</strong></h3>
                                    <div class="timeline-body">
                                        {{ $update->remarks }}
                                        @if($update->location)
                                            <br>
                                            <small class="text-muted"><i class="fas fa-map-marker-alt"></i> {{ $update->location }}</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        <div>
                            <i class="fas fa-clock bg-gray"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Column -->
        <div class="col-md-4">
            <!-- Add Status Update Card -->
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Add Status Update</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.shipment-status-updates.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="shipment_id" value="{{ $shipment->id }}">
                        
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control" required>
                                <option value="Pending" {{ $shipment->current_status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Picked Up" {{ $shipment->current_status == 'Picked Up' ? 'selected' : '' }}>Picked Up</option>
                                <option value="In Transit" {{ $shipment->current_status == 'In Transit' ? 'selected' : '' }}>In Transit</option>
                                <option value="Arrived at Facility" {{ $shipment->current_status == 'Arrived at Facility' ? 'selected' : '' }}>Arrived at Facility</option>
                                <option value="Out for Delivery" {{ $shipment->current_status == 'Out for Delivery' ? 'selected' : '' }}>Out for Delivery</option>
                                <option value="Delivered" {{ $shipment->current_status == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="On Hold" {{ $shipment->current_status == 'On Hold' ? 'selected' : '' }}>On Hold</option>
                                <option value="Cancelled" {{ $shipment->current_status == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Location</label>
                            <input type="text" name="location" class="form-control" placeholder="Current Location">
                        </div>
                        
                        <div class="form-group">
                            <label>Remarks</label>
                            <textarea name="remarks" class="form-control" rows="3" placeholder="Optional remarks"></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-success btn-block">
                            <i class="fas fa-plus"></i> Add Update
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quick Actions</h3>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.shipments.label', $shipment) }}" target="_blank" class="btn btn-app">
                        <i class="fas fa-print"></i> Print Label
                    </a>
                    <a href="{{ route('admin.shipments.invoice', $shipment) }}" target="_blank" class="btn btn-app">
                        <i class="fas fa-file-invoice"></i> Invoice
                    </a>
                    <a href="mailto:{{ $shipment->client->email }}?subject=Shipment Update: {{ $shipment->tracking_number }}" class="btn btn-app">
                        <i class="fas fa-envelope"></i> Email Client
                    </a>
                </div>
            </div>

        </div>
    </div>
@endsection

@php
$outstandingBalance = 0;
$invoiceId = null;
if ($shipment->invoices->isNotEmpty()) {
    $invoice = $shipment->invoices->first();
    $payments = $invoice->payments->sum('amount');
    $outstandingBalance = $invoice->total - $payments;
    $invoiceId = $invoice->id;
}
@endphp

@section('adminlte_js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusForm = document.querySelector('form[action="{{ route('admin.shipment-status-updates.store') }}"]');
        if (statusForm) {
            const statusSelect = statusForm.querySelector('select[name="status"]');
            const outstandingBalance = {{ $outstandingBalance }};
            const invoiceId = {{ $invoiceId ?? 'null' }};
            
            statusForm.addEventListener('submit', function(e) {
                const selectedStatus = statusSelect.value;
                const currentStatus = '{{ $shipment->current_status }}';
                
                if (selectedStatus === 'Picked Up' && currentStatus !== 'Picked Up') {
                    e.preventDefault();
                    
                    if (outstandingBalance > 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Outstanding Balance',
                            html: 'Cannot change status to "Picked Up".<br><br>' +
                                   '<strong>Outstanding Balance:</strong> {{ $shipment->currency ?? "USD" }} ' + outstandingBalance.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '<br><br>' +
                                   'Please pay the invoice first.',
                            showCancelButton: true,
                            confirmButtonText: '<i class="fas fa-credit-card"></i> Pay Invoice',
                            cancelButtonText: '<i class="fas fa-times"></i> Cancel',
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#dc3545'
                        }).then((result) => {
                            if (result.isConfirmed && invoiceId) {
                                window.location.href = '/admin/invoices/' + invoiceId;
                            }
                        });
                        return;
                    }
                    
                    Swal.fire({
                        icon: 'question',
                        title: 'Confirm Pickup',
                        html: 'Are you sure you want to mark this shipment as <strong>Picked Up</strong>?',
                        showCancelButton: true,
                        confirmButtonText: '<i class="fas fa-check"></i> Yes, Picked Up',
                        cancelButtonText: '<i class="fas fa-times"></i> Cancel',
                        confirmButtonColor: '#28a745',
                        cancelButtonColor: '#dc3545'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            statusForm.submit();
                        }
                    });
                }
            });
        }
    });
</script>
@endsection


@section('footer')
    <strong>Copyright &copy; {{ date('Y') }} <a href="#">LLC Express Logistics</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
        <b>Support Call</b> +256 703 948463
    </div>
@stop
