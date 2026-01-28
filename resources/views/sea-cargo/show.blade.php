@extends('adminlte::page')

@section('title', 'Sea Cargo Details')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1><i class="fas fa-ship"></i> Sea Cargo: {{ $shipment->tracking_number }}</h1>
        </div>
        <div class="col-sm-6">
            <a href="{{ route('admin.sea-cargo.edit', $shipment) }}" class="btn btn-warning float-right ml-2">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('admin.sea-cargo.index') }}" class="btn btn-secondary float-right">
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
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Shipment Details</h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Tracking Number:</dt>
                        <dd class="col-sm-8"><strong>{{ $shipment->tracking_number }}</strong></dd>

                        <dt class="col-sm-4">Client:</dt>
                        <dd class="col-sm-8">{{ $shipment->client->name }}</dd>

                        <dt class="col-sm-4">Origin:</dt>
                        <dd class="col-sm-8">{{ $shipment->origin }}</dd>

                        <dt class="col-sm-4">Destination:</dt>
                        <dd class="col-sm-8">{{ $shipment->destination }}</dd>

                        <dt class="col-sm-4">Delivery Time:</dt>
                        <dd class="col-sm-8"><span class="badge badge-info">{{ $shipment->delivery_range }}</span></dd>

                        <dt class="col-sm-4">Weight:</dt>
                        <dd class="col-sm-8">{{ $shipment->weight ? $shipment->weight . ' kg' : 'N/A' }}</dd>

                        <dt class="col-sm-4">Status:</dt>
                        <dd class="col-sm-8"><span class="badge badge-secondary">{{ $shipment->current_status }}</span></dd>

                        @if($shipment->batch)
                            <dt class="col-sm-4">Batch:</dt>
                            <dd class="col-sm-8">
                                <a href="{{ route('admin.batches.show', $shipment->batch) }}">
                                    <span class="badge badge-primary">{{ $shipment->batch->batch_number }}</span>
                                </a>
                            </dd>
                        @endif

                        @if($shipment->description)
                            <dt class="col-sm-4">Description:</dt>
                            <dd class="col-sm-8">{{ $shipment->description }}</dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Package Info</h3>
                </div>
                <div class="card-body">
                    <p><strong>Packages:</strong> {{ $shipment->num_packages ?? 'N/A' }}</p>
                    <p><strong>Type:</strong> {{ $shipment->package_type ? ucfirst($shipment->package_type) : 'N/A' }}</p>
                    <p><strong>Fragile:</strong> {{ $shipment->fragile ? 'Yes' : 'No' }}</p>
                </div>
            </div>

            @if($shipment->shipping_cost)
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Pricing</h3>
                </div>
                <div class="card-body">
                    <p><strong>Shipping:</strong> ${{ number_format($shipment->shipping_cost, 2) }}</p>
                    <p><strong>Tax:</strong> ${{ number_format($shipment->tax ?? 0, 2) }}</p>
                    <p><strong>Discount:</strong> ${{ number_format($shipment->discount ?? 0, 2) }}</p>
                    <hr>
                    <p><strong>Total:</strong> ${{ number_format($shipment->total_amount ?? 0, 2) }}</p>
                </div>
            </div>
            @endif
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
