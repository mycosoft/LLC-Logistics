@extends('adminlte::page')

@section('title', 'Batches')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Shipment Batches</h1>
        </div>
        <div class="col-sm-6">
            <a href="{{ route('admin.batches.create') }}" class="btn btn-primary float-right">
                <i class="fas fa-plus"></i> Create Batch
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

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Batch List</h3>
        </div>
        <div class="card-body">
            <!-- Filters -->
            <form action="{{ route('admin.batches.index') }}" method="GET" class="mb-3">
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Search by batch number or name" value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4">
                        <select name="status" class="form-control">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="in_transit" {{ request('status') == 'in_transit' ? 'selected' : '' }}>In Transit</option>
                            <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Batch Number</th>
                            <th>Name</th>
                            <th>Shipments</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th>Created Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($batches as $batch)
                            <tr>
                                <td><strong>{{ $batch->batch_number }}</strong></td>
                                <td>{{ $batch->name }}</td>
                                <td><span class="badge badge-info">{{ $batch->shipments->count() }}</span></td>
                                <td>
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
                                </td>
                                <td>{{ $batch->creator->name ?? 'N/A' }}</td>
                                <td>{{ $batch->created_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.batches.show', $batch) }}" class="btn btn-sm btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.batches.packing-list', $batch) }}" class="btn btn-sm btn-success" title="Packing List">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                    <a href="{{ route('admin.batches.edit', $batch) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.batches.destroy', $batch) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Delete this batch? Shipments will not be deleted, only unlinked from the batch.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No batches found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer clearfix">
            {{ $batches->appends(request()->query())->links('pagination::bootstrap-4') }}
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
