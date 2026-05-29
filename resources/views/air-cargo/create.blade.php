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
                        <div class="form-group" style="position: relative;">
                            <label for="client_search">Client <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" id="client_search" class="form-control @error('client_id') is-invalid @enderror"
                                       placeholder="Search client by name, phone or company..." required>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#quickAddClientModal" title="Quick Add Client">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <input type="hidden" name="client_id" id="client_id" value="{{ old('client_id') }}">
                            <div id="client_search_results" class="list-group" style="position: absolute; top: 100%; left: 0; right: 0; z-index: 1000; max-height: 200px; overflow-y: auto; display: none;"></div>
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
                            <label for="charge_type">Charge Type</label>
                            <select name="charge_type" id="charge_type" class="form-control">
                                <option value="">Select Charge Type</option>
                                <option value="per_kg" {{ old('charge_type') == 'per_kg' ? 'selected' : '' }}>Per Kg</option>
                                <option value="per_package" {{ old('charge_type') == 'per_package' ? 'selected' : '' }}>Per Package</option>
                                <option value="flat_rate" {{ old('charge_type') == 'flat_rate' ? 'selected' : '' }}>Flat Rate</option>
                            </select>
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
                <h5>Sender & Receiver</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Sender Information</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="sender_name"><strong>Sender Name</strong></label>
                                    <input type="text" name="sender_name" id="sender_name" class="form-control @error('sender_name') is-invalid @enderror" value="{{ old('sender_name') }}">
                                    @error('sender_name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="sender_phone"><strong>Sender Phone</strong></label>
                                    <input type="tel" name="sender_phone" id="sender_phone" class="form-control @error('sender_phone') is-invalid @enderror" value="{{ old('sender_phone') }}">
                                    @error('sender_phone')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="sender_address"><strong>Sender Address</strong></label>
                                    <textarea name="sender_address" id="sender_address" rows="3" class="form-control @error('sender_address') is-invalid @enderror">{{ old('sender_address') }}</textarea>
                                    @error('sender_address')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Receiver Information</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="receiver_name"><strong>Receiver Name</strong></label>
                                    <input type="text" name="receiver_name" id="receiver_name" class="form-control @error('receiver_name') is-invalid @enderror" value="{{ old('receiver_name') }}">
                                    @error('receiver_name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="receiver_phone"><strong>Receiver Phone</strong></label>
                                    <input type="tel" name="receiver_phone" id="receiver_phone" class="form-control @error('receiver_phone') is-invalid @enderror" value="{{ old('receiver_phone') }}">
                                    @error('receiver_phone')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="receiver_address"><strong>Receiver Address</strong></label>
                                    <textarea name="receiver_address" id="receiver_address" rows="3" class="form-control @error('receiver_address') is-invalid @enderror">{{ old('receiver_address') }}</textarea>
                                    @error('receiver_address')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
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
                                <option value="USD" {{ old('currency', 'USD') == 'USD' ? 'selected' : '' }}>$ USD (US Dollar)</option>
                                <option value="UGX" {{ old('currency') == 'UGX' ? 'selected' : '' }}>UGX (Ugandan Shilling)</option>
                                <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>€ EUR (Euro)</option>
                                <option value="GBP" {{ old('currency') == 'GBP' ? 'selected' : '' }}>£ GBP (British Pound)</option>
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
                                    <input type="text" name="items[0][rate]" class="form-control item-rate" placeholder="0.00" required>
                                </td>
                                <td>
                                    <input type="text" name="items[0][amount]" class="form-control item-amount" placeholder="0.00" readonly>
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
                                    <input type="text" id="subtotal_display" class="form-control-plaintext text-right font-weight-bold" value="0.00" readonly>
                                    <input type="hidden" name="shipping_cost" id="shipping_cost" value="0">
                                </td>
                            </tr>
                            <tr>
                                <td class="text-right"><strong>Tax:</strong></td>
                                <td>
                                    <input type="text" id="tax_display" class="form-control" value="{{ old('tax', '0.00') }}" placeholder="0.00">
                                    <input type="hidden" name="tax" id="tax" value="{{ old('tax', 0) }}">
                                </td>
                            </tr>
                            <tr>
                                <td class="text-right"><strong>Discount:</strong></td>
                                <td>
                                    <input type="text" id="discount_display" class="form-control" value="{{ old('discount', '0.00') }}" placeholder="0.00">
                                    <input type="hidden" name="discount" id="discount" value="{{ old('discount', 0) }}">
                                </td>
                            </tr>
                            <tr class="table-active">
                                <td class="text-right"><h5><strong>Total Amount:</strong></h5></td>
                                <td>
                                    <input type="text" id="total_amount_display" class="form-control font-weight-bold" value="{{ old('total_amount', '0.00') }}" readonly>
                                    <input type="hidden" name="total_amount" id="total_amount" value="{{ old('total_amount', 0) }}">
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

    @include('shared.quick-add-client-modal')
@stop

@section('js')
<script>
// Client Search Autocomplete
const clientSearchInput = document.getElementById('client_search');
const clientIdInput = document.getElementById('client_id');
const clientResults = document.getElementById('client_search_results');

clientSearchInput.addEventListener('input', function() {
    const query = this.value;
    
    if (query.length < 2) {
        clientResults.style.display = 'none';
        return;
    }
    
    const url = '{{ route("clients.search") }}?q=' + encodeURIComponent(query);
    
    fetch(url, {
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        clientResults.innerHTML = '';
        if (data.length > 0) {
            data.forEach(client => {
                const item = document.createElement('a');
                item.href = '#';
                item.className = 'list-group-item list-group-item-action';
                item.innerHTML = `<strong>${client.name}</strong><br><small>${client.phone || ''} ${client.company ? ' - ' + client.company : ''}</small>`;
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    clientSearchInput.value = client.name;
                    clientIdInput.value = client.id;
                    clientResults.style.display = 'none';
                });
                clientResults.appendChild(item);
            });
            clientResults.style.display = 'block';
        } else {
            clientResults.innerHTML = '<div class="list-group-item text-muted">No clients found. Use the <strong>+</strong> button to quick-add.</div>';
            clientResults.style.display = 'block';
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
});

document.addEventListener('click', function(e) {
    if (!clientSearchInput.contains(e.target) && !clientResults.contains(e.target)) {
        clientResults.style.display = 'none';
    }
});

// Listen for new client added from the modal
$(document).on('clientAdded', function(e, client) {
    clientSearchInput.value = client.name;
    clientIdInput.value = client.id;
});

// Format number with commas (e.g., 1,000,000.00)
function formatNumber(value) {
    const num = parseFloat(value) || 0;
    return num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

// Parse formatted number back to float (remove commas)
function parseFormattedNumber(str) {
    return parseFloat(String(str).replace(/,/g, '')) || 0;
}

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
                <input type="text" name="items[${itemIndex}][rate]" class="form-control item-rate" placeholder="0.00" required>
            </td>
            <td>
                <input type="text" name="items[${itemIndex}][amount]" class="form-control item-amount" placeholder="0.00" readonly>
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
    const rate = parseFormattedNumber(row.querySelector('.item-rate').value);
    const amount = quantity * rate;
    row.querySelector('.item-amount').value = formatNumber(amount);
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
        subtotal += parseFormattedNumber(input.value);
    });
    
    document.getElementById('subtotal_display').value = formatNumber(subtotal);
    document.getElementById('shipping_cost').value = subtotal.toFixed(2);
    
    calculateFinalTotal();
}

// Calculate final total (subtotal + tax - discount)
function calculateFinalTotal() {
    const subtotal = parseFloat(document.getElementById('shipping_cost').value) || 0;
    const tax = parseFormattedNumber(document.getElementById('tax_display').value);
    const discount = parseFormattedNumber(document.getElementById('discount_display').value);
    
    // Update hidden fields with raw values
    document.getElementById('tax').value = tax.toFixed(2);
    document.getElementById('discount').value = discount.toFixed(2);
    
    const total = subtotal + tax - discount;
    document.getElementById('total_amount').value = total.toFixed(2);
    document.getElementById('total_amount_display').value = formatNumber(total);
}

// Attach listeners to tax and discount display fields
document.getElementById('tax_display').addEventListener('input', calculateFinalTotal);
document.getElementById('discount_display').addEventListener('input', calculateFinalTotal);

// Initial setup
attachLineItemListeners();
updateRemoveButtons();
</script>
@stop

@section('footer')
    <strong>Copyright &copy; {{ date('Y') }} <a href="#">LLC Express Logistics</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
        <b>Support Call</b> +256 703 948463
    </div>
@stop
