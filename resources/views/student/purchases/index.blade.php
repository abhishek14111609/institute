@extends('layouts.app')

@section('title', 'My Purchases')

@section('sidebar')
    @include('student.sidebar')
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row g-4 mb-5">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 bg-dark text-white overflow-hidden p-2 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <h6 class="text-white-50 fw-bold mb-1 small text-uppercase" style="letter-spacing: 1px;">Purchase Ledger</h6>
                                <h2 class="fw-bold mb-0 display-6">&#8377;{{ number_format($stats['total_spent'], 2) }}</h2>
                                <p class="text-white-50 small mb-0 mt-2">All store-room purchases are marked paid and linked to downloadable invoices.</p>
                            </div>
                            <div class="bg-primary bg-opacity-10 p-3 rounded-circle border border-primary border-opacity-25">
                                <i class="bi bi-bag-check text-primary fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-2 overflow-hidden bg-white">
                    <div class="card-body p-4">
                        <div class="row g-3 text-center">
                            <div class="col-6">
                                <div class="rounded-4 bg-light p-3">
                                    <small class="text-muted tiny fw-bold d-block mb-1">ORDERS</small>
                                    <h4 class="fw-bold mb-0">{{ $stats['total_orders'] }}</h4>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="rounded-4 bg-light p-3">
                                    <small class="text-muted tiny fw-bold d-block mb-1">ITEMS</small>
                                    <h4 class="fw-bold mb-0">{{ $stats['total_items'] }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="alert alert-success border-0 mt-4 mb-0 rounded-4 small">
                            Every completed purchase includes the paid amount and invoice reference.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">Purchased Items</h5>
                <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2">Cash Sales</span>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive rounded-4 border overflow-hidden">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 small">DATE</th>
                                <th class="small">ITEM</th>
                                <th class="small text-center">QTY</th>
                                <th class="small text-end">PAID AMOUNT</th>
                                <th class="small">INVOICE</th>
                                <th class="small pe-4 text-end">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($purchases as $purchase)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark">{{ $purchase->created_at->format('d M, Y') }}</div>
                                        <div class="tiny text-muted">{{ $purchase->created_at->format('h:i A') }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $purchase->item->name ?? 'Deleted Item' }}</div>
                                        <div class="tiny text-muted">
                                            {{ $purchase->item->category ?? 'Inventory' }} • &#8377;{{ number_format($purchase->unit_price, 2) }} each
                                        </div>
                                    </td>
                                    <td class="text-center fw-bold">{{ $purchase->quantity }}</td>
                                    <td class="text-end">
                                        <div class="fw-bold text-success">&#8377;{{ number_format($purchase->total_amount, 2) }}</div>
                                        <div class="tiny text-muted">{{ strtoupper($purchase->payment_status ?? 'paid') }}</div>
                                    </td>
                                    <td>
                                        @if($purchase->invoice)
                                            <div class="fw-bold text-primary">{{ $purchase->invoice->invoice_number }}</div>
                                            <div class="tiny text-muted">{{ $purchase->invoice->invoice_date->format('d M, Y') }}</div>
                                        @else
                                            <span class="text-muted small">Pending</span>
                                        @endif
                                    </td>
                                    <td class="pe-4 text-end">
                                        @if($purchase->invoice)
                                            <div class="btn-group shadow-sm rounded-pill overflow-hidden border">
                                                <a href="{{ route('student.invoices.stream', $purchase->invoice) }}" target="_blank" class="btn btn-sm btn-white border-0" title="View Invoice">
                                                    <i class="bi bi-eye text-primary"></i>
                                                </a>
                                                <a href="{{ route('student.invoices.download', $purchase->invoice) }}" class="btn btn-sm btn-white border-0" title="Download Invoice">
                                                    <i class="bi bi-download text-success"></i>
                                                </a>
                                            </div>
                                        @else
                                            <span class="text-muted small">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="opacity-25 mb-3"><i class="bi bi-bag-x display-1"></i></div>
                                        <p class="text-muted small mb-0">No inventory purchases have been recorded for your account yet.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <style>
        .btn-white {
            background-color: #fff;
        }

        .btn-white:hover {
            background-color: #f8f9fa;
        }

        .tiny {
            font-size: 0.75rem;
        }
    </style>
@endsection
