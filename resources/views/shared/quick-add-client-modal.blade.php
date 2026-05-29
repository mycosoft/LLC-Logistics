{{-- Quick Add Client Modal --}}
<div class="modal fade" id="quickAddClientModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="quickAddClientForm">
                @csrf
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white">
                        <i class="fas fa-user-plus"></i> Quick Add Client
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger d-none" id="quickAddClientError"></div>
                    <div class="form-group">
                        <label for="quick_client_name">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="quick_client_name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="quick_client_email">Email</label>
                        <input type="email" class="form-control" id="quick_client_email" name="email" placeholder="optional">
                    </div>
                    <div class="form-group">
                        <label for="quick_client_phone">Phone <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="quick_client_phone" name="phone" required placeholder="e.g. 0703948463 (auto-formats to 256)">
                    </div>
                    <div class="form-group">
                        <label for="quick_client_company">Company</label>
                        <input type="text" class="form-control" id="quick_client_company" name="company">
                    </div>
                    <div class="form-group">
                        <label for="quick_client_address">Address</label>
                        <textarea class="form-control" id="quick_client_address" name="address" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="quickAddClientSubmit">
                        <i class="fas fa-save"></i> Add Client
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function() {
    var form = document.getElementById('quickAddClientForm');
    if (!form || form.dataset.qacInit) return;
    form.dataset.qacInit = '1';

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        var submitBtn = form.querySelector('#quickAddClientSubmit');
        var errorDiv = form.querySelector('#quickAddClientError');

        errorDiv.classList.add('d-none');
        errorDiv.innerHTML = '';
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';

        fetch('{{ route("admin.clients.quick-store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') || {}).content || ''
            },
            body: JSON.stringify({
                name: form.querySelector('#quick_client_name').value,
                phone: form.querySelector('#quick_client_phone').value,
                email: form.querySelector('#quick_client_email').value,
                company: form.querySelector('#quick_client_company').value,
                address: form.querySelector('#quick_client_address').value
            })
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (data.success) {
                $('#quickAddClientModal').modal('hide');
                form.reset();

                var clientSearchInput = document.getElementById('client_search');
                var clientIdInput = document.getElementById('client_id');
                if (clientSearchInput) clientSearchInput.value = data.client.name;
                if (clientIdInput) clientIdInput.value = data.client.id;

                // Trigger for jQuery event listeners
                if (typeof $ !== 'undefined') {
                    $(document).trigger('clientAdded', [data.client]);
                }
            } else {
                errorDiv.classList.remove('d-none');
                errorDiv.innerHTML = data.message || 'Failed to create client.';
            }
        })
        .catch(function(err) {
            errorDiv.classList.remove('d-none');
            errorDiv.innerHTML = 'Network error. Please try again.';
        })
        .finally(function() {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-save"></i> Add Client';
        });
    });

    $('#quickAddClientModal').on('hidden.bs.modal', function() {
        form.querySelector('#quickAddClientError').classList.add('d-none');
        form.querySelector('#quickAddClientError').innerHTML = '';
        form.reset();
    });
})();
</script>
