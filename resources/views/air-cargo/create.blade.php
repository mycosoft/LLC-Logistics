@extends('adminlte::page')

@section('title', 'Create Air Cargo Shipment')

@section('content_header')
    <h1><i class="fas fa-plane"></i> Create Air Cargo Shipment</h1>
@stop

@section('content')
    <form action="{{ route('admin.air-cargo.store') }}" method="POST">
        @csrf
        
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Shipment Information</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="client_id">Client <span class="text-danger">*</span></label>
                            <select name="client_id" id="client_id" class="form-control @error('client_id') is-invalid @enderror" required>
                                <option value="">Select Client</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
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
                                <option value="Pending" selected>Pending</option>
                                <option value="Picked Up">Picked Up</option>
                                <option value="In Transit">In Transit</option>
                                <option value="Delivered">Delivered</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="origin">Origin <span class="text-danger">*</span></label>
                            <input type="text" name="origin" id="origin" class="form-control @error('origin') is-invalid @enderror" value="{{ old('origin') }}" required>
                            @error('origin')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="destination">Destination <span class="text-danger">*</span></label>
                            <input type="text" name="destination" id="destination" class="form-control @error('destination') is-invalid @enderror" value="{{ old('destination') }}" required>
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
                                    <input type="number" name="delivery_time_min" class="form-control @error('delivery_time_min') is-invalid @enderror" placeholder="Min (e.g., 5)" value="{{ old('delivery_time_min') }}" min="1" required>
                                    @error('delivery_time_min')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6">
                                    <input type="number" name="delivery_time_max" class="form-control @error('delivery_time_max') is-invalid @enderror" placeholder="Max (e.g., 7)" value="{{ old('delivery_time_max') }}" min="1" required>
                                    @error('delivery_time_max')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <small class="form-text text-muted">Example: 5-7 days, 10-14 days</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="weight">Weight (kg)</label>
                            <input type="number" step="0.01" name="weight" id="weight" class="form-control" value="{{ old('weight') }}">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" rows="3" class="form-control">{{ old('description') }}</textarea>
                </div>

                <hr>
                <h5>Package Details (Optional)</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="num_packages">Number of Packages</label>
                            <input type="number" name="num_packages" id="num_packages" class="form-control" value="{{ old('num_packages') }}" min="1">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="package_type">Package Type</label>
                            <select name="package_type" id="package_type" class="form-control">
                                <option value="">Select Type</option>
                                <option value="box">Box</option>
                                <option value="pallet">Pallet</option>
                                <option value="envelope">Envelope</option>
                                <option value="custom">Custom</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="custom-control custom-checkbox mt-4">
                                <input type="checkbox" class="custom-control-input" id="fragile" name="fragile" value="1">
                                <label class="custom-control-label" for="fragile">Fragile Item</label>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>
                <h5>Pricing & Billing</h5>
                
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="currency">Currency</label>
                            <select name="currency" id="currency" class="form-control">
                                <option value="UGX" {{ old('currency', 'UGX') == 'UGX' ? 'selected' : '' }}>UGX (Shs)</option>
                                <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD ($)</option>
                                <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                                <option value="GBP" {{ old('currency') == 'GBP' ? 'selected' : '' }}>GBP (£)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <h6 class="mb-3">Invoice Line Items</h6>
                <div class="table-responsive">
                    <table class="table table-bordered" id="lineItemsTable">
                        <thead class="thead-light">
                            <tr>
                                <th width="40%">Description</th>
                                <th width="15%">Quantity</th>
                                <th width="20%">Rate</th>
                                <th width="20%">Amount</th>
                                <th width="5%"></th>
                            </tr>
                        </thead>
                        <tbody id="lineItemsBody">
                            <tr class="line-item-row">
                                <td>
                                    <input type="text" name="items[0][description]" class="form-control" placeholder="E.g., Air Freight Charges" required>
                                </td>
                                <td>
                                    <input type="number" name="items[0][quantity]" class="form-control item-quantity" value="1" min="1" required>
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="items[0][rate]" class="form-control item-rate" placeholder="0.00" required>
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="items[0][amount]" class="form-control item-amount" placeholder="0.00" readonly>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-danger remove-item" disabled>
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <button type="button" class="btn btn-sm btn-success mb-3" id="addLineItem">
                    <i class="fas fa-plus"></i> Add Line Item
                </button>

                <hr>

                <div class="row">
                    <div class="col-md-6 offset-md-6">
                        <table class="table table-sm">
                            <tr>
                                <td class="text-right"><strong>Subtotal:</strong></td>
                                <td width="150">
                                    <input type="number" step="0.01" id="subtotal_display" class="form-control-plaintext text-right font-weight-bold" value="0.00" readonly>
                                    <input type="hidden" name="shipping_cost" id="shipping_cost" value="0">
                                </td>
                            </tr>
                            <tr>
                                <td class="text-right"><strong>Tax:</strong></td>
                                <td>
                                    <input type="number" step="0.01" name="tax" id="tax" class="form-control" value="{{ old('tax', 0) }}">
                                </td>
                            </tr>
                            <tr>
                                <td class="text-right"><strong>Discount:</strong></td>
                                <td>
                                    <input type="number" step="0.01" name="discount" id="discount" class="form-control" value="{{ old('discount', 0) }}">
                                </td>
                            </tr>
                            <tr class="table-active">
                                <td class="text-right"><h5><strong>Total Amount:</strong></h5></td>
                                <td>
                                    <input type="number" step="0.01" name="total_amount" id="total_amount" class="form-control font-weight-bold" value="{{ old('total_amount', 0) }}" readonly>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="payment_method">Payment Method</label>
                            <select name="payment_method" id="payment_method" class="form-control">
                                <option value="">Select Method</option>
                                <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>Card</option>
                                <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="mobile_money" {{ old('payment_method') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="payment_status">Payment Status</label>
                            <select name="payment_status" id="payment_status" class="form-control">
                                <option value="pending" {{ old('payment_status', 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ old('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create Air Shipment
                </button>
                <a href="{{ route('admin.air-cargo.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </div>
    </form>
@stop

@section('js')
<script>
// Line Items Management
let itemIndex = 1;

// Add new line item
document.getElementById('addLineItem').addEventListener('click', function() {
    const tbody = document.getElementById('lineItemsBody');
    const newRow = `
        <tr class="line-item-row">
            <td>
                <input type="text" name="items[${itemIndex}][description]" class="form-control" placeholder="E.g., Handling Fee" required>
            </td>
            <td>
                <input type="number" name="items[${itemIndex}][quantity]" class="form-control item-quantity" value="1" min="1" required>
            </td>
            <td>
                <input type="number" step="0.01" name="items[${itemIndex}][rate]" class="form-control item-rate" placeholder="0.00" required>
            </td>
            <td>
                <input type="number" step="0.01" name="items[${itemIndex}][amount]" class="form-control item-amount" placeholder="0.00" readonly>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger remove-item">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `;
    tbody.insertAdjacentHTML('beforeend', newRow);
    itemIndex++;
    updateRemoveButtons();
    attachLineItemListeners();
});

// Remove line item
document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-item')) {
        const row = e.target.closest('tr');
        row.remove();
        updateRemoveButtons();
        calculateLineItemsTotal();
    }
});

// Update remove button states
function updateRemoveButtons() {
    const rows = document.querySelectorAll('.line-item-row');
    const removeButtons = document.querySelectorAll('.remove-item');
    removeButtons.forEach((btn, index) => {
        btn.disabled = rows.length === 1;
    });
}

// Calculate line item amount (quantity * rate)
function calculateLineItemAmount(row) {
    const quantity = parseFloat(row.querySelector('.item-quantity').value) || 0;
    const rate = parseFloat(row.querySelector('.item-rate').value) || 0;
    const amount = quantity * rate;
    row.querySelector('.item-amount').value = amount.toFixed(2);
    calculateLineItemsTotal();
}

// Attach listeners to line items
function attachLineItemListeners() {
    document.querySelectorAll('.line-item-row').forEach(row => {
        const quantityInput = row.querySelector('.item-quantity');
        const rateInput = row.querySelector('.item-rate');
        
        quantityInput.removeEventListener('input', () => calculateLineItemAmount(row));
        rateInput.removeEventListener('input', () => calculateLineItemAmount(row));
        
        quantityInput.addEventListener('input', () => calculateLineItemAmount(row));
        rateInput.addEventListener('input', () => calculateLineItemAmount(row));
    });
}

// Calculate subtotal from all line items
function calculateLineItemsTotal() {
    let subtotal = 0;
    document.querySelectorAll('.item-amount').forEach(input => {
        subtotal += parseFloat(input.value) || 0;
    });
    
    document.getElementById('subtotal_display').value = subtotal.toFixed(2);
    document.getElementById('shipping_cost').value = subtotal.toFixed(2);
    
    calculateFinalTotal();
}

// Calculate final total (subtotal + tax - discount)
function calculateFinalTotal() {
    const subtotal = parseFloat(document.getElementById('shipping_cost').value) || 0;
    const tax = parseFloat(document.getElementById('tax').value) || 0;
    const discount = parseFloat(document.getElementById('discount').value) || 0;
    
    const total = subtotal + tax - discount;
    document.getElementById('total_amount').value = total.toFixed(2);
}

// Attach listeners to tax and discount
document.getElementById('tax').addEventListener('input', calculateFinalTotal);
document.getElementById('discount').addEventListener('input', calculateFinalTotal);

// Initial setup
attachLineItemListeners();
updateRemoveButtons();
</script>
@stop

@section('footer')
    <strong>Copyright &copy; {{ date('Y') }} <a href="#">Bryanz Logistics</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
        <b>Support Call</b> 0750501151
    </div>
@stop
