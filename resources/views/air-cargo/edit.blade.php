@extends('adminlte::page')

@section('title', 'Edit Air Cargo Shipment')

@section('content_header')
    <h1><i class="fas fa-plane"></i> Edit Air Cargo Shipment</h1>
@stop

@section('content')
    <form action="{{ route('admin.air-cargo.update', $shipment) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Shipment Information</h3>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <strong>Tracking Number:</strong> {{ $shipment->tracking_number }}
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="client_id">Client <span class="text-danger">*</span></label>
                            <select name="client_id" id="client_id" class="form-control @error('client_id') is-invalid @enderror" required>
                                <option value="">Select Client</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id', $shipment->client_id) == $client->id ? 'selected' : '' }}>
                                        {{ $client->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('client_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="current_status">Status</label>
                            <select name="current_status" id="current_status" class="form-control">
                                <option value="Pending" {{ old('current_status', $shipment->current_status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Picked Up" {{ old('current_status', $shipment->current_status) == 'Picked Up' ? 'selected' : '' }}>Picked Up</option>
                                <option value="In Transit" {{ old('current_status', $shipment->current_status) == 'In Transit' ? 'selected' : '' }}>In Transit</option>
                                <option value="Delivered" {{ old('current_status', $shipment->current_status) == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="origin">Origin <span class="text-danger">*</span></label>
                            <input type="text" name="origin" id="origin" class="form-control @error('origin') is-invalid @enderror" value="{{ old('origin', $shipment->origin) }}" required>
                            @error('origin')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="destination">Destination <span class="text-danger">*</span></label>
                            <input type="text" name="destination" id="destination" class="form-control @error('destination') is-invalid @enderror" value="{{ old('destination', $shipment->destination) }}" required>
                            @error('destination')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Delivery Time (Days) <span class="text-danger">*</span></label>
                            <div class="row">
                                <div class="col-6">
                                    <input type="number" name="delivery_time_min" class="form-control @error('delivery_time_min') is-invalid @enderror" placeholder="Min" value="{{ old('delivery_time_min', $shipment->delivery_time_min) }}" min="1" required>
                                    @error('delivery_time_min')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6">
                                    <input type="number" name="delivery_time_max" class="form-control @error('delivery_time_max') is-invalid @enderror" placeholder="Max" value="{{ old('delivery_time_max', $shipment->delivery_time_max) }}" min="1" required>
                                    @error('delivery_time_max')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="weight">Weight (kg)</label>
                            <input type="number" step="0.01" name="weight" id="weight" class="form-control" value="{{ old('weight', $shipment->weight) }}">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" rows="3" class="form-control">{{ old('description', $shipment->description) }}</textarea>
                </div>

                <hr>
                <h5>Package Details</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="num_packages">Number of Packages</label>
                            <input type="number" name="num_packages" id="num_packages" class="form-control" value="{{ old('num_packages', $shipment->num_packages) }}" min="1">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="package_type">Package Type</label>
                            <select name="package_type" id="package_type" class="form-control">
                                <option value="">Select Type</option>
                                <option value="box" {{ old('package_type', $shipment->package_type) == 'box' ? 'selected' : '' }}>Box</option>
                                <option value="pallet" {{ old('package_type', $shipment->package_type) == 'pallet' ? 'selected' : '' }}>Pallet</option>
                                <option value="envelope" {{ old('package_type', $shipment->package_type) == 'envelope' ? 'selected' : '' }}>Envelope</option>
                                <option value="custom" {{ old('package_type', $shipment->package_type) == 'custom' ? 'selected' : '' }}>Custom</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="custom-control custom-checkbox mt-4">
                                <input type="checkbox" class="custom-control-input" id="fragile" name="fragile" value="1" {{ old('fragile', $shipment->fragile) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="fragile">Fragile Item</label>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>
                <h5>Pricing</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="shipping_cost">Shipping Cost</label>
                            <input type="number" step="0.01" name="shipping_cost" id="shipping_cost" class="form-control" value="{{ old('shipping_cost', $shipment->shipping_cost) }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="tax">Tax</label>
                            <input type="number" step="0.01" name="tax" id="tax" class="form-control" value="{{ old('tax', $shipment->tax) }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="discount">Discount</label>
                            <input type="number" step="0.01" name="discount" id="discount" class="form-control" value="{{ old('discount', $shipment->discount) }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Shipment
                </button>
                <a href="{{ route('admin.air-cargo.show', $shipment) }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </div>
    </form>
@stop

@section('footer')
    <strong>Copyright &copy; {{ date('Y') }} <a href="#">Bryanz Logistics</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
        <b>Support Call</b> 0750501151
    </div>
@stop
