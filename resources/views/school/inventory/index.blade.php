@extends('layouts.app')

@section('title', $isSport ? 'Academy Inventory' : 'Store Room Management')

@section('sidebar')
    @include('school.sidebar')
@endsection

@section('content')
    <div class="container-fluid py-4 text-dark font-outfit">
        @if (session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center" role="alert">
                <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                <div class="small fw-bold">{{ session('success') }}</div>
                @if (session('open_invoice_id'))
                    <a href="{{ route('school.invoices.stream', session('open_invoice_id')) }}" target="_blank"
                        class="btn btn-sm btn-light rounded-pill ms-3 px-3 fw-bold">
                        <i class="bi bi-receipt me-1"></i> View Invoice
                    </a>
                @endif
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                <div class="small fw-bold">{{ session('error') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-4 m-0">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <h3 class="fw-bold mb-1 text-primary">
                            {{ $isSport ? 'Kits & Equipment Stock' : 'Inventory & Stock Room' }}</h3>
                        <p class="text-muted small">Manage physical inventory, sell items to students, and generate instant
                            invoices.</p>
                    </div>
                    <div>
                        <button class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold me-2" data-bs-toggle="modal"
                            data-bs-target="#addItemModal">
                            <i class="bi bi-plus-lg me-2"></i> Add New Item
                        </button>
                        <button class="btn btn-success rounded-pill px-4 shadow-sm fw-bold" data-bs-toggle="modal"
                            data-bs-target="#issueItemModal">
                            <i class="bi bi-person-check me-2"></i> Issue/Sell to Student
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 bg-primary text-white">
                    <div class="card-body p-4 d-flex align-items-center">
                        <div class="bg-white bg-opacity-25 rounded-circle p-3 me-3">
                            <i class="bi bi-box-seam fs-3"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 opacity-75 small">Total In-Stock Items</h6>
                            <h2 class="fw-bold mb-0">{{ $items->sum('stock_quantity') }}</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 bg-danger text-white">
                    <div class="card-body p-4 d-flex align-items-center">
                        <div class="bg-white bg-opacity-25 rounded-circle p-3 me-3">
                            <i class="bi bi-exclamation-triangle-fill fs-3"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 opacity-75 small">Low Stock Alerts</h6>
                            <h2 class="fw-bold mb-0">
                                {{ $items->filter(fn($i) => $i->stock_quantity <= ($i->alert_quantity ?? 5))->count() }}
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 bg-info text-dark">
                    <div class="card-body p-4 d-flex align-items-center">
                        <div class="bg-white bg-opacity-25 rounded-circle p-3 me-3">
                            <i class="bi bi-currency-rupee fs-3"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 opacity-75 small">Total Inventory Value</h6>
                            <h2 class="fw-bold mb-0">
                                Rs{{ number_format($items->sum(function ($i) {return $i->price * $i->stock_quantity;}),0) }}
                            </h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-9">
                <div class="card border-0 shadow-sm rounded-4 min-vh-60">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                        <form action="{{ route('school.inventory.index') }}" method="GET" id="filterForm">
                            <div class="row g-3 align-items-center">
                                <div class="col-md-5">
                                    <h5 class="fw-bold mb-0">Stock Catalog</h5>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                                        <input type="text" name="search" class="form-control border-0 bg-light"
                                            placeholder="Search by name..." value="{{ request('search') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-select form-select-sm border-0 bg-light" name="category"
                                        onchange="this.form.submit()">
                                        <option value="All" {{ request('category') == 'All' ? 'selected' : '' }}>All
                                            Categories</option>
                                        <option value="Kit" {{ request('category') == 'Kit' ? 'selected' : '' }}>Kits &
                                            Gear</option>
                                        <option value="Book" {{ request('category') == 'Book' ? 'selected' : '' }}>Books
                                        </option>
                                        <option value="Uniform" {{ request('category') == 'Uniform' ? 'selected' : '' }}>
                                            Uniforms</option>
                                        <option value="Other" {{ request('category') == 'Other' ? 'selected' : '' }}>Other
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead class="small text-muted text-uppercase">
                                    <tr>
                                        <th class="border-0 ps-0">Item / Category</th>
                                        <th class="border-0 text-center">In Stock</th>
                                        <th class="border-0 text-center">Unit Price</th>
                                        <th class="border-0 text-center">Total Value</th>
                                        <th class="border-0 text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($items as $item)
                                        <tr>
                                            <td class="ps-0 border-light py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-light rounded-3 p-2 me-3 text-primary">
                                                        @if (stripos($item->category, 'book') !== false)
                                                            <i class="bi bi-book"></i>
                                                        @elseif(stripos($item->category, 'kit') !== false)
                                                            <i class="bi bi-trophy"></i>
                                                        @elseif(stripos($item->category, 'dress') !== false || stripos($item->category, 'uniform') !== false)
                                                            <i class="bi bi-person-badge"></i>
                                                        @else
                                                            <i class="bi bi-box"></i>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <h6 class="fw-bold mb-0">{{ $item->name }}</h6>
                                                        <span
                                                            class="badge bg-light text-dark small">{{ $item->category }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center border-light">
                                                @if ($item->stock_quantity <= ($item->alert_quantity ?? 5))
                                                    <span
                                                        class="badge bg-danger rounded-pill px-3">{{ $item->stock_quantity }}
                                                        Low Stock</span>
                                                @else
                                                    <span class="fw-bold">{{ $item->stock_quantity }}</span>
                                                @endif
                                            </td>
                                            <td class="border-light text-center">Rs{{ number_format($item->price, 2) }}
                                            </td>
                                            <td class="border-light text-center fw-bold text-primary">
                                                Rs{{ number_format($item->price * $item->stock_quantity) }}</td>
                                            <td class="text-end pe-0 border-light">
                                                <div class="dropdown">
                                                    <button class="btn btn-light btn-sm rounded-circle"
                                                        data-bs-toggle="dropdown">
                                                        <i class="bi bi-three-dots-vertical"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg">
                                                        <li><a class="dropdown-item py-2"
                                                                href="{{ route('school.inventory.edit', $item) }}"><i
                                                                    class="bi bi-pencil me-2 text-primary"></i> Edit
                                                                Stock</a></li>
                                                        <li>
                                                            <hr class="dropdown-divider">
                                                        </li>
                                                        <li>
                                                            <form action="{{ route('school.inventory.destroy', $item) }}"
                                                                method="POST"
                                                                onsubmit="return confirm('Remove this item from store?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="dropdown-item py-2 text-danger"><i
                                                                        class="bi bi-trash me-2"></i> Delete</button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5 opacity-25">
                                                <i class="bi bi-box display-1 d-block mb-3"></i>
                                                <h6>Your inventory is empty.</h6>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 h-100 min-vh-60">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                        <h5 class="fw-bold mb-0 text-dark">Recent Sales</h5>
                    </div>
                    <div class="card-body p-3">
                        <div class="list-group list-group-flush gap-2">
                            @forelse($recentSales as $sale)
                                <div class="list-group-item bg-light border-0 rounded-4 p-3 mb-2">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary text-white rounded-circle me-2 d-flex align-items-center justify-content-center"
                                                style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                {{ substr($sale->student->user->name ?? 'S', 0, 1) }}
                                            </div>
                                            <h6 class="fw-bold mb-0 small text-dark truncate-1"
                                                title="{{ $sale->student->user->name ?? 'Unknown' }}">
                                                {{ $sale->student->user->name ?? 'Unknown' }}</h6>
                                        </div>
                                        <span
                                            class="badge {{ ($sale->payment_status ?? 'unpaid') == 'paid' ? 'bg-success' : 'bg-warning' }} rounded-pill x-small">
                                            {{ strtoupper($sale->payment_status ?? 'UNPAID') }}
                                        </span>
                                    </div>
                                    <p class="small text-muted mb-1 truncate-1">{{ $sale->quantity }}x
                                        {{ $sale->item->name ?? 'Deleted Item' }}</p>
                                    <div class="d-flex justify-content-between align-items-center border-top pt-2 mt-2">
                                        <div>
                                            <span
                                                class="fw-bold text-primary small">Rs{{ number_format($sale->total_amount, 2) }}</span>
                                            @if ($sale->invoice)
                                                <div class="text-muted x-small">Invoice:
                                                    {{ $sale->invoice->invoice_number }}</div>
                                            @endif
                                        </div>
                                        <div class="text-end">
                                            <span
                                                class="text-muted x-small opacity-75 d-block">{{ $sale->created_at->diffForHumans(null, true) }}</span>
                                            @if ($sale->invoice)
                                                <a href="{{ route('school.invoices.stream', $sale->invoice) }}"
                                                    target="_blank" class="text-decoration-none x-small fw-bold">Open
                                                    Invoice</a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted py-5 opacity-50">
                                    <i class="bi bi-clock-history fs-1 mb-3 d-block"></i>
                                    <p class="small">No logs found.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('school.inventory.modals')

    <style>
        .font-outfit {
            font-family: 'Outfit', sans-serif;
        }

        .x-small {
            font-size: 0.7rem;
        }

        .min-vh-60 {
            min-height: 60vh;
        }

        .badge {
            font-weight: 600;
            padding: 0.4em 0.8em;
        }

        .list-group-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .05);
            transition: 0.3s cubic-bezier(.4, 0, .2, 1);
            cursor: pointer;
        }

        .truncate-1 {
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
@endsection

@section('scripts')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                dropdownParent: $('#issueItemModal')
            });

            let searchTimer;
            $('input[name="search"]').on('input', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(() => {
                    $('#filterForm').submit();
                }, 800);
            });
        });
    </script>
@endsection
