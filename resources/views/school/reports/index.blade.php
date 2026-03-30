@extends('layouts.app')

@section('title', 'Institutional Insight Center')

@section('sidebar')
    @include('school.sidebar')
@endsection

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <div>
                <h3 class="fw-bold mb-1 text-gradient">Reports Dashboard</h3>
                <p class="text-muted small mb-0">Student records, split revenue, expenses, and stock analytics in one place.</p>
            </div>
            <div class="bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill small fw-bold">
                <i class="bi bi-graph-up-arrow me-2"></i> Year {{ $year }}
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="text-muted small mb-1">Fee Revenue</div>
                        <h4 class="fw-bold text-primary">&#8377;{{ number_format(array_sum($incomeTrend['fee_amounts'] ?? []), 0) }}</h4>
                        <small class="text-muted">Collected this year</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="text-muted small mb-1">Selling Revenue</div>
                        <h4 class="fw-bold text-success">&#8377;{{ number_format(array_sum($incomeTrend['sales_amounts'] ?? []), 0) }}</h4>
                        <small class="text-muted">Inventory sales this year</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="text-muted small mb-1">{{ $label['expenses'] }}</div>
                        <h4 class="fw-bold text-danger">&#8377;{{ number_format($expenseTrend['total'], 0) }}</h4>
                        <small class="text-muted">Recorded this year</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="text-muted small mb-1">Net Balance</div>
                        <h4 class="fw-bold {{ ($incomeTrend['total'] - $expenseTrend['total']) >= 0 ? 'text-info' : 'text-danger' }}">
                            &#8377;{{ number_format($incomeTrend['total'] - $expenseTrend['total'], 0) }}
                        </h4>
                        <small class="text-muted">Revenue minus expenses</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="fw-bold mb-1">Student Record Report</h5>
                                <p class="text-muted small mb-0">Active vs inactive students.</p>
                            </div>
                            <span class="badge bg-light text-dark">Total {{ $studentRecords['total'] }}</span>
                        </div>
                        <div class="chart-wrap">
                            <canvas id="studentRecordChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="fw-bold mb-1">Monthly Revenue Split</h5>
                                <p class="text-muted small mb-0">Fee revenue vs inventory selling revenue month-wise.</p>
                            </div>
                            <span class="badge bg-success-subtle text-success">&#8377;{{ number_format($incomeTrend['total'], 0) }}</span>
                        </div>
                        <div class="chart-wrap">
                            <canvas id="monthlyIncomeChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="fw-bold mb-1">Monthly Expense</h5>
                                <p class="text-muted small mb-0">Expense outflow month-wise.</p>
                            </div>
                            <span class="badge bg-danger-subtle text-danger">&#8377;{{ number_format($expenseTrend['total'], 0) }}</span>
                        </div>
                        <div class="chart-wrap">
                            <canvas id="monthlyExpenseChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="fw-bold mb-1">Stock Report</h5>
                                <p class="text-muted small mb-0">Study material stock by type.</p>
                            </div>
                            <span class="badge bg-info-subtle text-info">Items {{ $stockReport['total_items'] }}</span>
                        </div>
                        <div class="chart-wrap">
                            <canvas id="stockReportChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('school.reports.income') }}" class="btn btn-outline-success btn-sm rounded-pill px-3">Detailed Income</a>
            <a href="{{ route('school.reports.expenses') }}" class="btn btn-outline-danger btn-sm rounded-pill px-3">Detailed Expenses</a>
            <a href="{{ route('school.reports.attendance') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">Attendance Report</a>
            <a href="{{ route('school.materials.index') }}" class="btn btn-outline-info btn-sm rounded-pill px-3">Detailed Stock</a>
            <a href="{{ route('school.reports.pending-fees') }}" class="btn btn-outline-warning btn-sm rounded-pill px-3">Pending Fees</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const studentData = @json($studentRecords);
        const incomeData = @json($incomeTrend);
        const expenseData = @json($expenseTrend);
        const stockData = @json($stockReport);

        const moneyTick = (value) => `₹${Number(value).toLocaleString('en-IN')}`;

        new Chart(document.getElementById('studentRecordChart'), {
            type: 'doughnut',
            data: {
                labels: studentData.labels,
                datasets: [{
                    data: studentData.counts,
                    backgroundColor: ['#198754', '#dc3545'],
                    borderWidth: 0,
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        new Chart(document.getElementById('monthlyIncomeChart'), {
            type: 'line',
            data: {
                labels: incomeData.labels,
                datasets: [{
                    label: 'Fee Revenue',
                    data: incomeData.fee_amounts,
                    fill: true,
                    tension: 0.35,
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.08)',
                    pointRadius: 3,
                }, {
                    label: 'Selling Revenue',
                    data: incomeData.sales_amounts,
                    fill: true,
                    tension: 0.35,
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.08)',
                    pointRadius: 3,
                }, {
                    label: 'Total Revenue',
                    data: incomeData.amounts,
                    tension: 0.35,
                    borderColor: '#6610f2',
                    borderDash: [6, 4],
                    pointRadius: 2,
                }]
            },
            options: {
                maintainAspectRatio: false,
                scales: {
                    y: {
                        ticks: {
                            callback: moneyTick
                        }
                    }
                }
            }
        });

        new Chart(document.getElementById('monthlyExpenseChart'), {
            type: 'bar',
            data: {
                labels: expenseData.labels,
                datasets: [{
                    label: 'Expense',
                    data: expenseData.amounts,
                    backgroundColor: 'rgba(220, 53, 69, 0.75)',
                    borderRadius: 6,
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        ticks: {
                            callback: moneyTick
                        }
                    }
                }
            }
        });

        new Chart(document.getElementById('stockReportChart'), {
            type: 'bar',
            data: {
                labels: stockData.type_labels.length ? stockData.type_labels : ['NO DATA'],
                datasets: [{
                    label: 'Stock Items',
                    data: stockData.type_totals.length ? stockData.type_totals : [0],
                    backgroundColor: 'rgba(13, 202, 240, 0.75)',
                    borderRadius: 6,
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        precision: 0,
                        beginAtZero: true,
                    }
                }
            }
        });
    </script>

    <style>
        .text-gradient {
            background: linear-gradient(135deg, #0d6efd 0%, #198754 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .chart-wrap {
            position: relative;
            height: 300px;
        }

        @media (max-width: 991.98px) {
            .chart-wrap {
                height: 260px;
            }
        }
    </style>
@endsection
