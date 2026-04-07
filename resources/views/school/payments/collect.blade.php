@extends('layouts.app')

@section('title', 'Unified Fee Collection Hub')

@section('sidebar')
    @include('school.sidebar')
@endsection

@section('content')
    <div class="collection-hub-wrapper">
        <!-- Premium Hero Header -->
        {{-- <div class="hub-hero py-5 px-4 mb-4">
            <div class="container-fluid position-relative z-index-1">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <div class="badge bg-white bg-opacity-10 text-white rounded-pill px-3 py-2 mb-3 backdrop-blur border border-white border-opacity-20 animate-fade-in-down small tracking-widest">
                            <i class="bi bi-lightning-charge-fill me-1"></i> FINANCE CENTER
                        </div>
                        <h1 class="display-5 fw-800 text-white mb-2 tracking-tight animate-fade-in-up">Unified Collection Hub</h1>
                        <p class="text-white opacity-70 lead mb-0 animate-fade-in-up delay-100">
                            Scan, retrieve, and process multiple {{ $isSport ? 'athletic' : 'educational' }} fees in a single high-speed transaction.
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end mt-4 mt-lg-0 animate-fade-in-right delay-200">
                        <div class="btn-group glass-group p-1 rounded-pill border border-white border-opacity-10 shadow-lg">
                            <a href="{{ route('school.fees.index') }}" class="btn btn-glass-light rounded-pill px-4 border-0 fw-bold">
                                <i class="bi bi-list-columns-reverse me-2"></i> All Fees
                            </a>
                            <a href="{{ route('school.students.index') }}" class="btn btn-glass-light rounded-pill px-4 border-0 fw-bold">
                                <i class="bi bi-people me-2"></i> {{ $isSport ? 'Athletes' : 'Students' }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Dynamic Background Shapes -->
            <div class="abstract-shape shape-1"></div>
            <div class="abstract-shape shape-2"></div>
        </div> --}}

        <div class="container-fluid py-2 pb-5">
            <div class="row justify-content-center">
                <div class="col-xl-11">

                    <!-- Enhanced Selection Section -->
                    <div class="card selection-card border-0 shadow-soft rounded-5 mb-5 animate-zoom-in">
                        <div class="card-body p-5">
                            <div class="row align-items-center">
                                <div class="col-lg-6 mb-4 mb-lg-0 text-center text-lg-start">
                                    <h4 class="fw-800 text-dark mb-2">{{ $isSport ? 'Member' : 'Student' }} Ledger Retrieval
                                    </h4>
                                    <p class="text-muted mb-2">Select a {{ $isSport ? 'athlete' : 'student' }} profile below
                                        to view active {{ $isSport ? 'memberships' : 'enrollments' }} and outstanding dues.
                                    </p>
                                    <div class="tiny text-muted">
                                        Showing <span class="fw-800 text-dark">all</span> students in your school
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="student-search-panel">
                                        <label for="student_search_input"
                                            class="form-label tiny fw-800 text-muted text-uppercase mb-2 ms-2">Search
                                            {{ $isSport ? 'athlete' : 'student' }}</label>
                                        <div class="position-relative">
                                            <input id="student_search_input" type="text"
                                                class="form-control form-select-xl hub-select rounded-pill px-4 shadow-sm fw-bold border-2"
                                                placeholder="Type name or roll number"
                                                value="{{ $student ? $student->user->name . ' (Roll: ' . ($student->roll_number ?: 'N/A') . ')' : '' }}"
                                                autocomplete="off">
                                            <div id="student_suggestions" class="student-suggestions d-none"></div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-2 px-2">
                                        <small class="text-muted">
                                            {{ $students->count() }}
                                            {{ $isSport ? 'athlete' : 'student' }}{{ $students->count() === 1 ? '' : 's' }}
                                            found
                                        </small>
                                        <small id="student_hint" class="text-muted">Type to filter, then click a student to
                                            open the ledger.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($student)
                        <form action="{{ route('school.payments.bulk-store') }}" method="POST" id="bulk_payment_form"
                            class="animate-fade-in-up">
                            @csrf
                            <input type="hidden" name="student_id" value="{{ $student->id }}">

                            <div class="card border-0 shadow-soft rounded-5 overflow-hidden">
                                <div
                                    class="card-header bg-white py-4 px-4 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="avatar-sm bg-primary-gradient rounded-circle me-3 d-flex align-items-center justify-content-center text-white">
                                            <i class="bi bi-file-earmark-text"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-800 text-dark mb-0">Complete Fee Collection Form</h6>
                                            <small class="text-muted">Fill all fields and submit one consolidated payment
                                                request.</small>
                                        </div>
                                    </div>
                                    <span
                                        class="badge bg-soft-rose text-rose rounded-pill px-3 py-2 fw-bold border border-rose border-opacity-10">
                                        {{ $pendingFees->count() }} PENDING ITEMS
                                    </span>
                                </div>

                                <div class="p-4">
                                    @forelse($pendingFees as $index => $fee)
                                        @php $remaining = $fee->getRemainingAmount(); @endphp
                                        <div class="border rounded-4 p-3 mb-3 bg-white shadow-sm fee-item-card"
                                            data-total="{{ (float) $fee->total_amount }}"
                                            data-paid="{{ (float) $fee->paid_amount }}">
                                            <input type="hidden" name="payments[{{ $index }}][fee_id]"
                                                value="{{ $fee->id }}">

                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <label class="form-label tiny fw-800 text-muted text-uppercase mb-1">Fee
                                                        Type</label>
                                                    <input type="text" class="form-control rounded-pill"
                                                        value="{{ ucfirst(str_replace('_', ' ', $fee->fee_type)) }}"
                                                        readonly>
                                                </div>
                                                <div class="col-md-4">
                                                    <label
                                                        class="form-label tiny fw-800 text-muted text-uppercase mb-1">{{ $isSport ? 'Member Group / Session' : 'Class Group / Batch' }}</label>
                                                    <input type="text" class="form-control rounded-pill"
                                                        value="{{ $fee->batch->name ?? 'General' }}" readonly>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label tiny fw-800 text-muted text-uppercase mb-1">Due
                                                        Date</label>
                                                    <input type="text" class="form-control rounded-pill"
                                                        value="{{ $fee->due_date->format('d M, Y') }}" readonly>
                                                </div>

                                                <div class="col-md-3">
                                                    <label
                                                        class="form-label tiny fw-800 text-muted text-uppercase mb-1">Total</label>
                                                    <input type="text" class="form-control rounded-pill"
                                                        value="₹{{ number_format($fee->total_amount, 2) }}" readonly>
                                                </div>
                                                <div class="col-md-3">
                                                    <label
                                                        class="form-label tiny fw-800 text-muted text-uppercase mb-1">Paid</label>
                                                    <input type="text" class="form-control rounded-pill"
                                                        value="₹{{ number_format($fee->paid_amount, 2) }}" readonly>
                                                </div>
                                                <div class="col-md-2">
                                                    <label
                                                        class="form-label tiny fw-800 text-muted text-uppercase mb-1">Discount</label>
                                                    <input type="number" class="form-control rounded-pill fee-discount"
                                                        name="payments[{{ $index }}][discount]"
                                                        value="{{ old("payments.$index.discount", (float) ($fee->discount ?? 0)) }}"
                                                        min="0" step="0.01">
                                                </div>
                                                <div class="col-md-2">
                                                    <label
                                                        class="form-label tiny fw-800 text-muted text-uppercase mb-1">Late
                                                        Fee</label>
                                                    <input type="number" class="form-control rounded-pill fee-late"
                                                        name="payments[{{ $index }}][late_fee]"
                                                        value="{{ old("payments.$index.late_fee", (float) ($fee->late_fee ?? 0)) }}"
                                                        min="0" step="0.01">
                                                </div>
                                                <div class="col-md-2">
                                                    <label
                                                        class="form-label tiny fw-800 text-muted text-uppercase mb-1">Remaining</label>
                                                    <input type="text"
                                                        class="form-control rounded-pill fw-bold fee-remaining-display"
                                                        value="₹{{ number_format($remaining, 2) }}" readonly>
                                                </div>

                                                <div class="col-md-12">
                                                    <label
                                                        class="form-label tiny fw-800 text-muted text-uppercase mb-1">Settling
                                                        Amount</label>
                                                    <div
                                                        class="input-group input-group-settle rounded-pill overflow-hidden border shadow-sm transition-focus">
                                                        <span
                                                            class="input-group-text border-0 bg-white text-muted tiny fw-bold">₹</span>
                                                        <input type="number"
                                                            name="payments[{{ $index }}][amount]"
                                                            class="form-control border-0 shadow-none payment-input fw-bold text-primary"
                                                            step="0.01" min="0" max="{{ $remaining }}"
                                                            data-max="{{ $remaining }}" value="0"
                                                            oninput="calculateTotal()">
                                                        <button class="btn btn-soft-primary border-0 px-3" type="button"
                                                            onclick="this.previousElementSibling.value = this.previousElementSibling.dataset.max; calculateTotal();">
                                                            Full
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-5">
                                            <div class="empty-state-icon mb-4 animate-bounce">
                                                <div class="bg-soft-success rounded-circle d-flex align-items-center justify-content-center mx-auto"
                                                    style="width: 80px; height: 80px;">
                                                    <i class="bi bi-shield-check text-success display-5"></i>
                                                </div>
                                            </div>
                                            <h5 class="fw-800 text-dark mb-1">Clear Balance!</h5>
                                            <p class="text-muted px-5">This {{ $isSport ? 'athlete' : 'student' }} has
                                                settled all active institutional dues. No current financial action required.
                                            </p>
                                        </div>
                                    @endforelse

                                    <hr class="my-4">

                                    <div class="row g-4 align-items-start">
                                        <div class="col-lg-4">
                                            <div class="bg-dark rounded-4 p-4 text-white h-100">
                                                <h6
                                                    class="fw-800 text-uppercase tiny tracking-widest opacity-60 mb-2 text-white">
                                                    Checkout Summary</h6>
                                                <div class="display-5 fw-800 text-primary-light">₹<span
                                                        id="grand_total">0.00</span></div>
                                                <div class="tiny opacity-50 mt-1">TOTAL CONSOLIDATED AMOUNT</div>
                                            </div>
                                        </div>

                                        <div class="col-lg-8">
                                            <div class="row g-3 mb-4">
                                                <div class="col-md-6">
                                                    <label
                                                        class="form-label tiny fw-800 text-muted text-uppercase mb-2">Transaction
                                                        Medium</label>
                                                    <div class="payment-grid">
                                                        <div class="pay-option">
                                                            <input type="radio" name="payment_method" value="cash"
                                                                id="m_cash" checked>
                                                            <label for="m_cash" class="pay-card"><i
                                                                    class="bi bi-cash-stack"></i><span>CASH</span></label>
                                                        </div>
                                                        <div class="pay-option">
                                                            <input type="radio" name="payment_method" value="upi"
                                                                id="m_upi">
                                                            <label for="m_upi" class="pay-card"><i
                                                                    class="bi bi-qr-code-scan"></i><span>UPI</span></label>
                                                        </div>
                                                        <div class="pay-option">
                                                            <input type="radio" name="payment_method"
                                                                value="bank_transfer" id="m_bank">
                                                            <label for="m_bank" class="pay-card"><i
                                                                    class="bi bi-bank"></i><span>BANK</span></label>
                                                        </div>
                                                        <div class="pay-option">
                                                            <input type="radio" name="payment_method" value="card"
                                                                id="m_card">
                                                            <label for="m_card" class="pay-card"><i
                                                                    class="bi bi-credit-card"></i><span>CARD</span></label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <label
                                                        class="form-label tiny fw-800 text-muted text-uppercase mb-2">Collection
                                                        Date</label>
                                                    <input type="date" name="paid_at"
                                                        class="form-control rounded-pill px-4 py-2 border-light bg-light fw-bold mb-3"
                                                        value="{{ date('Y-m-d') }}" required>

                                                    <label
                                                        class="form-label tiny fw-800 text-muted text-uppercase mb-2">Reference
                                                        / Ref No.</label>
                                                    <input type="text" name="transaction_id"
                                                        class="form-control rounded-pill px-4 py-2 border-light bg-light fw-bold"
                                                        placeholder="Cheque # or UPI Ref">
                                                </div>
                                            </div>

                                            <div class="mb-4">
                                                <label
                                                    class="form-label tiny fw-800 text-muted text-uppercase mb-2">Internal
                                                    Remarks</label>
                                                <textarea name="notes" class="form-control rounded-4 px-3 py-2 border-light bg-light small" rows="2"
                                                    placeholder="Administrative notes..."></textarea>
                                            </div>

                                            <button type="submit"
                                                class="btn btn-primary btn-xl w-100 rounded-pill py-3 fw-bold shadow-strong animate-pulse transition-scale"
                                                id="submit_btn" disabled>
                                                <i class="bi bi-check2-all me-2"></i> CONFIRM COLLECTION
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        const collectBaseUrl = "{{ url('/school/payments/collect') }}";
        const studentSearchInput = document.getElementById('student_search_input');
        const studentSuggestions = document.getElementById('student_suggestions');
        const studentSearchPanel = document.querySelector('.student-search-panel');

        const allStudents = [
            @foreach ($students as $studentItem)
                {
                    id: {{ $studentItem->id }},
                    name: @json($studentItem->user->name),
                    roll: @json($studentItem->roll_number ?: 'N/A'),
                    course: @json($studentItem->course ? $studentItem->course->name : ''),
                },
            @endforeach
        ];

        function currentStudentUrl(studentId) {
            return `${collectBaseUrl}/${studentId}`;
        }

        function studentLabel(student) {
            const courseSuffix = student.course ? ` - ${student.course}` : '';
            return `${student.name} (Roll: ${student.roll})${courseSuffix}`;
        }

        function renderStudentSuggestions(query) {
            if (!studentSuggestions) {
                return;
            }

            const normalized = query.trim().toLowerCase();
            const matches = allStudents.filter(student => {
                const name = (student.name || '').toLowerCase();
                const roll = (student.roll || '').toLowerCase();
                return normalized === '' || name.startsWith(normalized) || roll.startsWith(normalized);
            }).slice(0, 10);

            if (!matches.length) {
                studentSuggestions.innerHTML = '<div class="student-suggestion-empty">No students found</div>';
                adjustSuggestionPlacement();
                studentSuggestions.classList.remove('d-none');
                return;
            }

            studentSuggestions.innerHTML = matches.map(student => `
                <button type="button" class="student-suggestion-item" data-student-id="${student.id}">
                    <span class="student-suggestion-name">${student.name}</span>
                    <span class="student-suggestion-meta">Roll: ${student.roll}${student.course ? ' · ' + student.course : ''}</span>
                </button>
            `).join('');

            adjustSuggestionPlacement();
            studentSuggestions.classList.remove('d-none');
        }

        function openStudentFromQuery() {
            if (!studentSearchInput) {
                return;
            }

            const value = studentSearchInput.value.trim().toLowerCase();
            if (!value) {
                renderStudentSuggestions('');
                return;
            }

            const matches = allStudents.filter(student => {
                const name = (student.name || '').toLowerCase();
                const roll = (student.roll || '').toLowerCase();
                return name.startsWith(value) || roll.startsWith(value);
            });

            if (matches.length === 1) {
                window.location.href = currentStudentUrl(matches[0].id);
                return;
            }

            renderStudentSuggestions(value);
        }

        function adjustSuggestionPlacement() {
            if (!studentSearchInput || !studentSuggestions) {
                return;
            }

            const inputRect = studentSearchInput.getBoundingClientRect();
            const viewportHeight = window.innerHeight;
            const spaceBelow = viewportHeight - inputRect.bottom - 16;
            const spaceAbove = inputRect.top - 16;

            const shouldOpenUp = spaceBelow < 220 && spaceAbove > spaceBelow;
            const maxHeight = Math.max(140, Math.min(320, shouldOpenUp ? spaceAbove - 8 : spaceBelow - 8));
            const panelTop = shouldOpenUp ? Math.max(8, inputRect.top - maxHeight - 8) : inputRect.bottom + 8;

            studentSuggestions.style.left = `${inputRect.left}px`;
            studentSuggestions.style.width = `${inputRect.width}px`;
            studentSuggestions.style.top = `${panelTop}px`;
            studentSuggestions.style.maxHeight = `${maxHeight}px`;
        }

        function calculateTotal() {
            const grandTotalEl = document.getElementById('grand_total');
            const submitBtn = document.getElementById('submit_btn');

            // Ledger widgets are only present after selecting a student.
            if (!grandTotalEl || !submitBtn) {
                return;
            }

            let inputs = document.querySelectorAll('.payment-input');
            let total = 0;
            inputs.forEach(input => {
                const max = parseFloat(input.dataset.max || '0') || 0;
                let val = parseFloat(input.value) || 0;
                if (val < 0) {
                    val = 0;
                    input.value = '0';
                }
                if (val > max) {
                    val = max;
                    input.value = max.toFixed(2);
                }
                total += val;
            });

            const formattedTotal = total.toLocaleString('en-IN', {
                minimumFractionDigits: 2
            });
            grandTotalEl.innerText = formattedTotal;
            submitBtn.disabled = total <= 0;
            if (total > 0) {
                submitBtn.innerHTML = `<i class="bi bi-wallet2 me-2"></i> COLLECT ₹${formattedTotal}`;
            } else {
                submitBtn.innerHTML = `<i class="bi bi-check2-all me-2"></i> CONFIRM COLLECTION`;
            }
        }

        function autoAllocate() {
            let lumpSum = parseFloat(document.getElementById('lump_sum').value) || 0;
            if (lumpSum <= 0) return;

            let inputs = document.querySelectorAll('.payment-input');
            inputs.forEach(input => input.value = 0);

            inputs.forEach(input => {
                if (lumpSum <= 0) return;
                let max = parseFloat(input.dataset.max) || 0;
                let allocate = Math.min(lumpSum, max);
                input.value = allocate.toFixed(2);
                lumpSum -= allocate;

                // Add a little highlight effect
                input.closest('.input-group-settle').classList.add('allocate-flash');
                setTimeout(() => input.closest('.input-group-settle').classList.remove('allocate-flash'), 500);
            });

            calculateTotal();
        }

        function recalculateFeeCard(card) {
            if (!card) {
                return;
            }

            const total = parseFloat(card.dataset.total || '0') || 0;
            const paid = parseFloat(card.dataset.paid || '0') || 0;

            const discountInput = card.querySelector('.fee-discount');
            const lateInput = card.querySelector('.fee-late');
            const paymentInput = card.querySelector('.payment-input');
            const remainingDisplay = card.querySelector('.fee-remaining-display');

            let discount = parseFloat(discountInput?.value || '0') || 0;
            let lateFee = parseFloat(lateInput?.value || '0') || 0;

            if (discount < 0) {
                discount = 0;
                if (discountInput) {
                    discountInput.value = '0';
                }
            }

            if (lateFee < 0) {
                lateFee = 0;
                if (lateInput) {
                    lateInput.value = '0';
                }
            }

            const remaining = Math.max(0, total + lateFee - discount - paid);

            if (remainingDisplay) {
                remainingDisplay.value = `₹${remaining.toFixed(2)}`;
            }

            if (paymentInput) {
                paymentInput.max = remaining.toFixed(2);
                paymentInput.dataset.max = remaining.toFixed(2);

                let currentAmount = parseFloat(paymentInput.value || '0') || 0;
                if (currentAmount < 0) {
                    currentAmount = 0;
                    paymentInput.value = '0';
                }
                if (currentAmount > remaining) {
                    paymentInput.value = remaining.toFixed(2);
                }
            }
        }

        function setupFeeCalculationBindings() {
            const cards = document.querySelectorAll('.fee-item-card');

            cards.forEach(card => {
                const discountInput = card.querySelector('.fee-discount');
                const lateInput = card.querySelector('.fee-late');

                if (discountInput) {
                    discountInput.addEventListener('input', function() {
                        recalculateFeeCard(card);
                        calculateTotal();
                    });
                }

                if (lateInput) {
                    lateInput.addEventListener('input', function() {
                        recalculateFeeCard(card);
                        calculateTotal();
                    });
                }

                recalculateFeeCard(card);
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            setupFeeCalculationBindings();
            calculateTotal();

            if (studentSearchInput && studentSuggestions) {
                // Move dropdown to body so no parent container can clip it.
                if (studentSuggestions.parentElement !== document.body) {
                    document.body.appendChild(studentSuggestions);
                }

                studentSearchInput.addEventListener('input', function() {
                    renderStudentSuggestions(this.value);
                });

                studentSearchInput.addEventListener('focus', function() {
                    renderStudentSuggestions(this.value);
                });

                studentSearchInput.addEventListener('keydown', function(event) {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        openStudentFromQuery();
                    }
                });

                document.addEventListener('click', function(event) {
                    const clickedInsideInput = !!event.target.closest('.student-search-panel');
                    const clickedInsideDropdown = !!event.target.closest('#student_suggestions');
                    if (!clickedInsideInput && !clickedInsideDropdown) {
                        studentSuggestions.classList.add('d-none');
                    }
                });

                window.addEventListener('resize', adjustSuggestionPlacement);
                window.addEventListener('scroll', adjustSuggestionPlacement, true);

                studentSuggestions.addEventListener('click', function(event) {
                    const target = event.target.closest('[data-student-id]');
                    if (!target) {
                        return;
                    }

                    window.location.href = currentStudentUrl(target.dataset.studentId);
                });

                renderStudentSuggestions('');
            }
        });
    </script>

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            --hero-gradient: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
            --indigo: #4338ca;
            --rose: #e11d48;
            --text-rose: #be123c;
            --bg-rose: rgba(225, 29, 72, 0.08);
            --bg-indigo: rgba(67, 56, 202, 0.08);
        }

        .fw-800 {
            font-weight: 800;
        }

        .tracking-tight {
            letter-spacing: -1.5px;
        }

        .tracking-widest {
            letter-spacing: 2px;
        }

        .backdrop-blur {
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .shadow-soft {
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
        }

        .shadow-strong {
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.15);
        }

        .transition-base {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .transition-scale:active {
            transform: scale(0.98);
        }

        .transition-focus:focus-within {
            border-color: #4f46e5 !important;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1) !important;
        }

        /* Hero Styling */
        .hub-hero {
            background: var(--hero-gradient);
            border-radius: 0 0 3rem 3rem;
            position: relative;
            overflow: hidden;
            margin-top: -1.5rem;
        }

        .abstract-shape {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            z-index: 0;
            opacity: 0.15;
        }

        .shape-1 {
            width: 300px;
            height: 300px;
            background: #c026d3;
            top: -100px;
            right: -50px;
        }

        .shape-2 {
            width: 200px;
            height: 200px;
            background: #6366f1;
            bottom: -50px;
            left: 10%;
        }

        .glass-group {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
        }

        .btn-glass-light {
            color: white;
            background: transparent;
        }

        .btn-glass-light:hover {
            background: rgba(255, 255, 255, 0.15);
            color: white;
        }

        /* Select Enhancement */
        .hub-select {
            border: 2px solid #e2e8f0;
            background-color: #f8fafc;
            height: 60px;
            transition: all 0.3s;
        }

        .hub-select:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 5px rgba(79, 70, 229, 0.1);
            background-color: #fff;
        }

        .student-search-panel {
            position: relative;
            z-index: 30;
        }

        .student-suggestions {
            position: fixed;
            z-index: 2000;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            box-shadow: 0 18px 36px rgba(15, 23, 42, 0.16);
            max-height: 280px;
            overflow-y: auto;
        }

        .student-suggestions::-webkit-scrollbar {
            width: 8px;
        }

        .student-suggestions::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 99px;
        }

        .student-suggestion-item,
        .student-suggestion-empty {
            width: 100%;
            border: 0;
            background: #fff;
            text-align: left;
            padding: 0.85rem 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.15rem;
        }

        .student-suggestion-item+.student-suggestion-item {
            border-top: 1px solid #f1f5f9;
        }

        .student-suggestion-item:hover {
            background: rgba(79, 70, 229, 0.08);
        }

        .student-suggestion-item:focus-visible {
            outline: 2px solid #4f46e5;
            outline-offset: -2px;
            background: rgba(79, 70, 229, 0.08);
        }

        .student-suggestion-name {
            font-weight: 800;
            color: #111827;
        }

        .student-suggestion-meta {
            font-size: 0.85rem;
            color: #6b7280;
        }

        /* Table & Ledger Styling */
        .bg-light-indigo {
            background: var(--bg-indigo);
        }

        .text-indigo {
            color: var(--indigo);
        }

        .bg-soft-rose {
            background: var(--bg-rose);
        }

        .text-rose {
            color: var(--text-rose);
        }

        .ledger-row:hover {
            background-color: rgba(79, 70, 229, 0.02);
        }

        .bg-primary-gradient {
            background: var(--primary-gradient);
        }

        /* Payment Grid */
        .payment-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }

        .pay-option input {
            display: none;
        }

        .pay-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 15px;
            border: 2px solid #f1f5f9;
            border-radius: 1rem;
            cursor: pointer;
            transition: all 0.25s;
            background: #f8fafc;
        }

        .pay-card i {
            font-size: 1.5rem;
            margin-bottom: 5px;
            color: #64748b;
        }

        .pay-card span {
            font-size: 0.6rem;
            font-weight: 800;
            letter-spacing: 1px;
            color: #64748b;
        }

        .pay-option input:checked+.pay-card {
            border-color: #4f46e5;
            background: rgba(79, 70, 229, 0.05);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(79, 70, 229, 0.15);
        }

        .pay-option input:checked+.pay-card i {
            color: #4f46e5;
        }

        .pay-option input:checked+.pay-card span {
            color: #4f46e5;
        }

        .pay-card:hover {
            border-color: #cbd5e1;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateX(20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes zoomIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes pulseSlight {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.02);
            }

            100% {
                transform: scale(1);
            }
        }

        @keyframes bounceSlight {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        @keyframes flashBorder {
            0% {
                border-color: transparent;
            }

            50% {
                border-color: #4f46e5;
                background-color: rgba(79, 70, 229, 0.05);
            }

            100% {
                border-color: transparent;
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        .animate-fade-in-down {
            animation: fadeInDown 0.6s ease-out forwards;
        }

        .animate-fade-in-right {
            animation: fadeInRight 0.6s ease-out forwards;
        }

        .animate-zoom-in {
            animation: zoomIn 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        }

        .animate-pulse {
            animation: pulseSlight 2s infinite ease-in-out;
        }

        .animate-bounce {
            animation: bounceSlight 3s infinite ease-in-out;
        }

        .allocate-flash {
            animation: flashBorder 0.6s ease-out;
        }

        .delay-100 {
            animation-delay: 0.1s;
        }

        .delay-200 {
            animation-delay: 0.2s;
        }

        .text-primary-light {
            color: #818cf8;
        }

        .btn-primary {
            background: var(--primary-gradient);
            border: none;
        }

        .btn-indigo {
            background: #4338ca;
            color: white;
        }

        .btn-xl {
            font-size: 1.1rem;
        }
    </style>
@endsection
