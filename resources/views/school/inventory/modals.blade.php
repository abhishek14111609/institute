<!-- Modal: Add Item -->
<div class="modal fade" id="addItemModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-primary text-white border-0 py-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-box-seam me-2"></i> Register New Stock</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('school.inventory.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">ITEM NAME</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Physics Textbook Grade 10" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted">CATEGORY</label>
                            <select name="category" class="form-select" required>
                                <option value="Kit">Kit / Sport Gear</option>
                                <option value="Book">Educational Book</option>
                                <option value="Uniform">Uniform / Dress</option>
                                <option value="Other">Other Equipment</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted">UNIT PRICE (₹)</label>
                            <input type="number" name="price" class="form-control" placeholder="0.00" step="0.01" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted">INITIAL STOCK</label>
                            <input type="number" name="stock_quantity" class="form-control" placeholder="0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted">LOW STOCK ALERT AT</label>
                            <input type="number" name="alert_quantity" class="form-control" value="5">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow">Save Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Issue/Sell Item -->
<div class="modal fade" id="issueItemModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-success text-white border-0 py-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-cart-plus me-2"></i> Issue / Sell Item</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('school.inventory.issue') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">SELECT STUDENT</label>
                        <select name="student_id" class="form-select select2" required style="width: 100%;">
                            <option value="">— Search Student —</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}">{{ $student->user->name ?? 'Student' }} (Roll: {{ $student->roll_number }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label small fw-bold text-muted">CHOOSE ITEM</label>
                            <select name="item_id" class="form-select" required>
                                <option value="">— Select Available Stock —</option>
                                @foreach($items as $item)
                                    @if($item->stock_quantity > 0)
                                        <option value="{{ $item->id }}">{{ $item->name }} (Qty: {{ $item->stock_quantity }} | ₹{{ $item->price }})</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label small fw-bold text-muted">QUANTITY</label>
                            <input type="number" name="quantity" class="form-control" value="1" min="1" required>
                        </div>
                    </div>
                    <div class="alert alert-info py-2 small mb-0 mt-2">
                         Note: This sale is recorded as a paid cash transaction and the invoice is generated immediately.
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 shadow">Confirm Cash Sale</button>
                </div>
            </form>
        </div>
    </div>
</div>
