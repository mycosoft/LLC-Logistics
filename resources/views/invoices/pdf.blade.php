<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'DejaVu Sans', sans-serif; 
            color: #333; 
            line-height: 1.6;
            padding: 20px;
        }
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
        }
        .invoice-header {
            display: table;
            width: 100%;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #2c3e50;
        }
        .company-logo {
            max-width: 120px;
            height: auto;
            margin-bottom: 10px;
        }
        .company-details {
            display: table-cell;
            width: 60%;
            vertical-align: top;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 8px;
        }
        .company-info {
            font-size: 11px;
            color: #666;
            line-height: 1.6;
        }
        .invoice-title-section {
            display: table-cell;
            width: 40%;
            text-align: right;
            vertical-align: top;
        }
        .invoice-title {
            font-size: 32px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 8px;
        }
        .invoice-meta {
            font-size: 11px;
            color: #666;
        }
        .parties-section {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .party-box {
            display: table-cell;
            width: 48%;
            background: #f8f9fa;
            padding: 15px;
            vertical-align: top;
        }
        .party-box:first-child {
            margin-right: 4%;
        }
        .party-title {
            font-size: 10px;
            font-weight: bold;
            color: #2c3e50;
            text-transform: uppercase;
            margin-bottom: 8px;
            letter-spacing: 1px;
        }
        .party-details {
            font-size: 11px;
            line-height: 1.6;
        }
        .shipment-details {
            margin-bottom: 20px;
            padding: 12px;
            background: #f8f9fa;
            font-size: 11px;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .invoice-table thead {
            background: #2c3e50;
            color: white;
        }
        .invoice-table th {
            padding: 10px;
            text-align: left;
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
        }
        .invoice-table td {
            padding: 10px;
            border-bottom: 1px solid #e9ecef;
            font-size: 11px;
        }
        .text-right {
            text-align: right;
        }
        .totals-section {
            margin-left: auto;
            width: 300px;
            margin-bottom: 20px;
        }
        .total-row {
            display: table;
            width: 100%;
            padding: 6px 0;
            font-size: 11px;
        }
        .total-row > span {
            display: table-cell;
        }
        .total-row > span:last-child {
            text-align: right;
        }
        .total-row.subtotal {
            border-top: 1px solid #e9ecef;
        }
        .total-row.grand-total {
            border-top: 2px solid #2c3e50;
            margin-top: 8px;
            padding-top: 10px;
            font-size: 14px;
            font-weight: bold;
            color: #2c3e50;
        }
        .invoice-footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e9ecef;
            text-align: center;
            font-size: 11px;
        }
        .payment-status {
            display: inline-block;
            padding: 6px 15px;
            border-radius: 15px;
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 15px;
        }
        .status-paid { background: #d4edda; color: #155724; }
        .status-sent { background: #d1ecf1; color: #0c5460; }
        .status-overdue { background: #f8d7da; color: #721c24; }
        .status-draft { background: #e2e3e5; color: #383d41; }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="invoice-header">
            <div class="company-details">
                @if(file_exists(public_path($companySettings['logo'])))
                <img src="{{ public_path($companySettings['logo']) }}" alt="{{ $companySettings['name'] }}" class="company-logo">
                @endif
                <div class="company-name">{{ $companySettings['name'] }}</div>
                <div class="company-info">
                    {{ $companySettings['address'] }}<br>
                    Phone: {{ $companySettings['phone'] }}<br>
                    Email: {{ $companySettings['email'] }}
                </div>
            </div>
            <div class="invoice-title-section">
                <div class="invoice-title">INVOICE</div>
                <div class="invoice-meta">
                    <strong>Invoice #:</strong> {{ $invoice->invoice_number }}<br>
                    <strong>Date:</strong> {{ $invoice->issue_date->format('M d, Y') }}<br>
                    @if($invoice->due_date)
                    <strong>Due Date:</strong> {{ $invoice->due_date->format('M d, Y') }}<br>
                    @endif
                    <strong>Tracking #:</strong> {{ $shipment->tracking_number }}
                </div>
            </div>
        </div>

        <!-- Bill To / Ship To -->
        <div class="parties-section">
            <div class="party-box">
                <div class="party-title">Bill To</div>
                <div class="party-details">
                    <strong>{{ $shipment->client->name }}</strong><br>
                    @if($shipment->client->company)
                    {{ $shipment->client->company }}<br>
                    @endif
                    @if($shipment->client->address)
                    {{ $shipment->client->address }}<br>
                    @endif
                    {{ $shipment->client->email }}<br>
                    {{ $shipment->client->phone }}
                </div>
            </div>
            <div class="party-box">
                <div class="party-title">Ship To</div>
                <div class="party-details">
                    @if($shipment->receiver)
                        <strong>{{ $shipment->receiver->name }}</strong><br>
                        @if($shipment->receiver->company)
                        {{ $shipment->receiver->company }}<br>
                        @endif
                        @if($shipment->receiver->address)
                        {{ $shipment->receiver->address }}<br>
                        @endif
                        {{ $shipment->receiver->email }}<br>
                        {{ $shipment->receiver->phone }}
                    @else
                        <strong>{{ $shipment->receiver_name ?? 'N/A' }}</strong><br>
                        @if($shipment->receiver_address)
                        {{ $shipment->receiver_address }}<br>
                        @endif
                        {{ $shipment->receiver_phone ?? '' }}
                    @endif
                </div>
            </div>
        </div>

        <!-- Shipment Details -->
        <div class="shipment-details">
            <strong>Shipment Details:</strong> {{ $shipment->description ?? 'Logistics Service' }}<br>
            <strong>Route:</strong> {{ $shipment->origin }} → {{ $shipment->destination }}<br>
            <strong>Service Type:</strong> {{ ucfirst($shipment->service_type ?? 'Standard') }} | 
            <strong>Shipment Type:</strong> {{ ucfirst($shipment->shipment_type) }}
        </div>

        <!-- Invoice Items -->
        <table class="invoice-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>Shipping Charges</strong><br>
                        <small style="color: #666;">{{ ucfirst($shipment->service_type ?? 'Standard') }} Service</small>
                    </td>
                    <td class="text-right">{{ $shipment->currency ?? 'USD' }} {{ number_format($shipment->shipping_cost, 2) }}</td>
                </tr>
                @if($shipment->insurance_value > 0)
                <tr>
                    <td>
                        <strong>Insurance Coverage</strong><br>
                        <small style="color: #666;">Package Protection</small>
                    </td>
                    <td class="text-right">{{ $shipment->currency ?? 'USD' }} {{ number_format($shipment->insurance_value, 2) }}</td>
                </tr>
                @endif
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals-section">
            <div class="total-row subtotal">
                <span>Subtotal:</span>
                <span>{{ $shipment->currency ?? 'USD' }} {{ number_format($invoice->subtotal, 2) }}</span>
            </div>
            @if($invoice->tax > 0)
            <div class="total-row">
                <span>Tax:</span>
                <span>{{ $shipment->currency ?? 'USD' }} {{ number_format($invoice->tax, 2) }}</span>
            </div>
            @endif
            @if($invoice->discount > 0)
            <div class="total-row">
                <span>Discount:</span>
                <span>-{{ $shipment->currency ?? 'USD' }} {{ number_format($invoice->discount, 2) }}</span>
            </div>
            @endif
            <div class="total-row grand-total">
                <span>TOTAL:</span>
                <span>{{ $shipment->currency ?? 'USD' }} {{ number_format($invoice->total, 2) }}</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="invoice-footer">
            <div class="payment-status status-{{ $invoice->status }}">
                Status: {{ ucfirst($invoice->status) }}
            </div>
            <div>
                Thank you for choosing {{ $companySettings['name'] }}!<br>
                <small style="color: #999;">For inquiries, contact us at {{ $companySettings['email'] }} or {{ $companySettings['phone'] }}</small>
            </div>
        </div>
    </div>
</body>
</html>
