@extends('adminlte::page')

@section('title', 'Reports')

@section('content_header')
    <h1>Reports & Analytics</h1>
@stop

@section('content')
    <div class="row">
        <!-- Revenue Report -->
        <div class="col-lg-4 col-md-6">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <i class="fas fa-chart-line fa-4x text-primary mb-3"></i>
                    </div>
                    <h3 class="profile-username text-center">Revenue Report</h3>
                    <p class="text-muted text-center">View detailed revenue and payment history</p>
                    <a href="{{ route('admin.reports.revenue') }}" class="btn btn-primary btn-block">
                        <i class="fas fa-eye"></i> View Report
                    </a>
                </div>
            </div>
        </div>

        <!-- Outstanding Invoices -->
        <div class="col-lg-4 col-md-6">
            <div class="card card-warning card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <i class="fas fa-exclamation-circle fa-4x text-warning mb-3"></i>
                    </div>
                    <h3 class="profile-username text-center">Outstanding Invoices</h3>
                    <p class="text-muted text-center">Track unpaid and pending invoices</p>
                    <a href="{{ route('admin.reports.outstanding') }}" class="btn btn-warning btn-block">
                        <i class="fas fa-eye"></i> View Report
                    </a>
                </div>
            </div>
        </div>

        <!-- Payment History -->
        <div class="col-lg-4 col-md-6">
            <div class="card card-success card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <i class="fas fa-money-bill-wave fa-4x text-success mb-3"></i>
                    </div>
                    <h3 class="profile-username text-center">Payment History</h3>
                    <p class="text-muted text-center">Complete payment transaction history</p>
                    <a href="{{ route('admin.reports.payments') }}" class="btn btn-success btn-block">
                        <i class="fas fa-eye"></i> View Report
                    </a>
                </div>
            </div>
        </div>

        <!-- Shipment Report -->
        <div class="col-lg-4 col-md-6">
            <div class="card card-info card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <i class="fas fa-shipping-fast fa-4x text-info mb-3"></i>
                    </div>
                    <h3 class="profile-username text-center">Shipment Report</h3>
                    <p class="text-muted text-center">View all shipments with filters and analytics</p>
                    <a href="{{ route('admin.reports.shipments') }}" class="btn btn-info btn-block">
                        <i class="fas fa-eye"></i> View Report
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Quick Stats -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quick Statistics</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-sm-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary"><i class="fas fa-dollar-sign"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Revenue</span>
                                    @php
                                        $totalRevenue = \App\Models\Payment::sum('amount');
                                    @endphp
                                    <span class="info-box-number">UGX {{ number_format($totalRevenue, 0) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-calendar"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">This Month</span>
                                    @php
                                        $monthlyRevenue = \App\Models\Payment::whereYear('payment_date', now()->year)
                                            ->whereMonth('payment_date', now()->month)
                                            ->sum('amount');
                                    @endphp
                                    <span class="info-box-number">UGX {{ number_format($monthlyRevenue, 0) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-exclamation-triangle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Outstanding</span>
                                    @php
                                        $outstanding = \App\Models\Invoice::where('status', '!=', 'paid')
                                            ->where('status', '!=', 'cancelled')
                                            ->sum(\DB::raw('total - (SELECT COALESCE(SUM(amount), 0) FROM payments WHERE payments.invoice_id = invoices.id)'));
                                    @endphp
                                    <span class="info-box-number">UGX {{ number_format($outstanding, 0) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-file-invoice"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Invoices</span>
                                    @php
                                        $totalInvoices = \App\Models\Invoice::count();
                                    @endphp
                                    <span class="info-box-number">{{ $totalInvoices }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
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
