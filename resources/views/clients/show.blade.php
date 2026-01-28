@extends('adminlte::page')

@section('title', 'Client Details')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Client Details</h1>
        </div>
        <div class="col-sm-6">
            <a href="{{ route('clients.edit', $client) }}" class="btn btn-warning float-right">
                <i class="fas fa-edit"></i> Edit Client
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Client Information</h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Name:</dt>
                        <dd class="col-sm-8">{{ $client->name }}</dd>

                        <dt class="col-sm-4">Email:</dt>
                        <dd class="col-sm-8">{{ $client->email }}</dd>

                        <dt class="col-sm-4">Phone:</dt>
                        <dd class="col-sm-8">{{ $client->phone }}</dd>

                        <dt class="col-sm-4">Company:</dt>
                        <dd class="col-sm-8">{{ $client->company ?? 'N/A' }}</dd>

                        <dt class="col-sm-4">Address:</dt>
                        <dd class="col-sm-8">{{ $client->address ?? 'N/A' }}</dd>

                        <dt class="col-sm-4">Created:</dt>
                        <dd class="col-sm-8">{{ $client->created_at->format('M d, Y H:i') }}</dd>

                        <dt class="col-sm-4">Updated:</dt>
                        <dd class="col-sm-8">{{ $client->updated_at->format('M d, Y H:i') }}</dd>
                    </dl>
                </div>
                <div class="card-footer">
                    <a href="{{ route('clients.index') }}" class="btn btn-default">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Shipments</h3>
                </div>
                <div class="card-body">
                    @if($client->shipments->count() > 0)
                        <ul class="list-group">
                            @foreach($client->shipments as $shipment)
                                <li class="list-group-item">
                                    <strong>{{ $shipment->tracking_number }}</strong><br>
                                    <small>{{ $shipment->origin }} → {{ $shipment->destination }}</small><br>
                                    <span class="badge badge-info">{{ $shipment->current_status }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">No shipments yet.</p>
                    @endif
                </div>
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

