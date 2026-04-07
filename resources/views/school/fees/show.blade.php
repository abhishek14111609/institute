@extends('layouts.app')

@section('title', 'Institutional Ledger Breakdown')

@section('sidebar')
    @include('school.sidebar')
@endsection

@section('content')
    @php
        $student = $fee->student;
        $studentUser = $student?->user;
        $studentName = $studentUser?->name ?? 'Unknown Student';
        $studentEmail = $studentUser?->email ?? 'N/A';
        $studentRoll = $student?->roll_number ?? 'N/A';
        $studentBatchName = $fee->batch?->name ?? ($student?->batch?->name ?? 'General');
        $remainingAmount = $fee->getRemainingAmount();
        $statusMap = [
            'paid' => ['label' => 'SETTLED', 'color' => 'success'],
            'partial' => ['label' => 'PARTIAL', 'color' => 'warning'],
            'overdue' => ['label' => 'CRITICAL', 'color' => 'danger'],
            'pending' => ['label' => 'ACTIVE', 'color' => 'secondary'],
        ][$fee->status] ?? ['label' => 'UNKNOWN', 'color' => 'dark'];
    @endphp

    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-start align-items-md-center mb-4 gap-3 flex-wrap">
            <div>
                <h3 class="fw-bold mb-1 text-gradient">Institutional Ledger Card</h3>
                <p class="text-muted small mb-0">Detailed breakdown of student financial obligations and transaction history.
                </p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('school.fees.edit', $fee) }}"
                    class="btn btn-warning rounded-pill px-4 shadow-sm border-0 fw-bold small">
                    <i class="bi bi-pencil-square me-2"></i> Revise Ledger
                </a>
                @if ($fee->paid_amount == 0)
                    <form action="{{ route('school.fees.destroy', $fee) }}" method="POST"
                        onsubmit="return confirm('Nullify this ledger entry?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="btn btn-light text-danger border rounded-pill px-4 shadow-sm fw-bold small">
                            <i class="bi bi-trash3 me-2"></i> Revoke
                        </button>
                    </form>
                @endif
                <a href="{{ route('school.fees.index') }}"
                    class="btn btn-light border rounded-pill px-4 shadow-sm fw-bold small">
                    <i class="bi bi-arrow-left me-2"></i> Collection Desk
                </a>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold"
                                style="width: 56px; height: 56px; font-size: 20px;">
                                {{ strtoupper(substr($studentName, 0, 1)) }}
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold">{{ $studentName }}</h5>
                                <small class="text-muted">Roll No: {{ $studentRoll }}</small>
                            </div>
                        </div>

                        <div class="p-2 rounded-3 bg-primary bg-opacity-10 border border-primary border-opacity-10 mb-2">
                            <small class="text-primary tiny d-block">Linked Session / Batch</small>
                            <span class="small fw-bold text-primary"><i
                                    class="bi bi-tag-fill me-1"></i>{{ $studentBatchName }}</span>
                        </div>

                        <div class="p-2 rounded-3 bg-light border border-white mb-2">
                            <small class="text-muted tiny d-block">Communication Channel</small>
                            <span class="small fw-bold text-dark">{{ $studentEmail }}</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <span class="small text-muted">Dossier Status</span>
                            <span
                                class="badge bg-{{ $statusMap['color'] }} rounded-pill px-3 py-2 tiny fw-bold">{{ $statusMap['label'] }}</span>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 mb-4"
                    style="border-bottom: 4px solid var(--{{ $fee->status === 'paid' ? 'success' : ($fee->status === 'overdue' ? 'danger' : 'warning') }}) !important;">
                    <div class="card-body p-4">
                        <h6 class="tiny fw-bold text-muted text-uppercase mb-4" style="letter-spacing: 1px;">Asset Breakdown
                        </h6>
                        <div class="d-flex flex-column gap-3">
                            <div class="d-flex justify-content-between">
                                <span class="small text-muted">Total Obligation:</span>
                                <span class="small fw-bold text-dark">₹{{ number_format($fee->total_amount, 0) }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="small text-muted">Settled Capital:</span>
                                <span class="small fw-bold text-success">₹{{ number_format($fee->paid_amount, 0) }}</span>
                            </div>
                            @if ($fee->late_fee > 0)
                                <div class="d-flex justify-content-between">
                                    <span class="small text-muted">Late Penalty:</span>
                                    <span class="small fw-bold text-danger">₹{{ number_format($fee->late_fee, 0) }}</span>
                                </div>
                            @endif
                            <hr class="my-1 opacity-10">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-dark">Institutional Dues:</span>
                                <span class="h5 mb-0 fw-bold text-primary">₹{{ number_format($remainingAmount, 0) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div
                        class="card-header bg-white py-3 px-4 border-bottom-0 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-clock-history me-2 text-primary"></i> Asset
                            Settlement History</h6>
                        @if ($fee->status !== 'paid')
                            <a href="{{ route('school.payments.collect', $fee->student_id) }}"
                                class="btn btn-soft-success btn-sm rounded-pill px-4 fw-bold">
                                <i class="bi bi-plus-lg me-1"></i> Record Settlement
                            </a>
                        @endif
                    </div>
                    <div class="card-body p-0">
                        @if ($fee->payments->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr class="tiny text-muted text-uppercase fw-bold">
                                            <th class="ps-4">Chronology</th>
                                            <th>Disbursed Capital</th>
                                            <th>Settlement Mode</th>
                                            <th>Auth ID</th>
                                            <th class="pe-4 text-end">Administration</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($fee->payments as $payment)
                                            <tr class="transition-all hover-lift">
                                                <td class="ps-4">
                                                    <div class="small fw-bold text-dark">
                                                        {{ $payment->paid_at->format('d M, Y') }}</div>
                                                    <small
                                                        class="text-muted tiny">{{ $payment->paid_at->format('h:i A') }}</small>
                                                </td>
                                                <td><span class="small fw-bold text-success">+
                                                        ₹{{ number_format($payment->amount, 0) }}</span></td>
                                                <td>
                                                    <span
                                                        class="badge bg-soft-info text-info rounded-pill px-3 py-1 tiny fw-bold">
                                                        {{ strtoupper(str_replace('_', ' ', $payment->payment_method)) }}
                                                    </span>
                                                </td>
                                                <td><small
                                                        class="text-muted tiny fw-bold">#{{ $payment->transaction_id ?? 'N/A' }}</small>
                                                </td>
                                                <td class="pe-4 text-end">
                                                    @if ($payment->invoice)
                                                        <a href="{{ route('school.invoices.stream', $payment->invoice->id) }}"
                                                            target="_blank"
                                                            class="btn btn-sm btn-outline-primary rounded-pill px-3 me-1">
                                                            <i class="bi bi-receipt"></i>
                                                        </a>
                                                    @endif
                                                    <form action="{{ route('school.payments.destroy', $payment) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('Delete this payment?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                                            <i class="bi bi-trash3"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div class="display-6 text-muted mb-2"><i class="bi bi-receipt-cutoff"></i></div>
                                <h5 class="fw-bold mb-1">No settlements recorded yet</h5>
                                <p class="text-muted mb-0">Use Record Settlement to add the first payment entry.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .bg-soft-info {
            background-color: rgba(13, 202, 240, 0.1);
            color: #0dcaf0;
        }

        .btn-soft-success {
            background-color: rgba(25, 135, 84, 0.1);
            color: #198754;
        }

        .btn-soft-success:hover {
            background-color: #198754;
            color: #fff;
        }

        .text-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hover-lift:hover {
            transform: translateY(-3px);
        }

        .tiny {
            font-size: 0.65rem;
            letter-spacing: 0.5px;
        }
    </style>
@endsection
