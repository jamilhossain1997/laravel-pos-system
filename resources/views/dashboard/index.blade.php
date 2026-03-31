@extends('layouts.app')
@section('title','Dashboard')
@section('content')
<div class="page-header">
    <div>
        <div class="page-title">Dashboard</div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Overview</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('pos.index') }}" class="btn btn-primary">
        <i class="bi bi-bag-plus me-1"></i> New Sale
    </a>
</div>

{{-- Stat cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-label">Today Sales</div>
            <div class="stat-value text-success">{{ number_format($today_sales,2) }} SAR</div>
            <div class="stat-change text-muted">{{ $today_invoices }} invoices today</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-label">Month Sales</div>
            <div class="stat-value text-primary">{{ number_format($month_sales,2) }} SAR</div>
            <div class="stat-change text-muted">Current month</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-label">Net Profit (Month)</div>
            <div class="stat-value {{ $month_profit >= 0 ? 'text-success' : 'text-danger' }}">
                {{ number_format($month_profit,2) }} SAR
            </div>
            <div class="stat-change text-muted">Income - Expense</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-label">Total Clients</div>
            <div class="stat-value text-info">{{ $total_clients }}</div>
            <div class="stat-change text-muted">{{ $total_products }} products</div>
        </div>
    </div>
</div>

{{-- Charts row --}}
<div class="row g-3 mb-4">
    <div class="col-md-7">
        <div class="table-card p-3">
            <div class="table-card-header px-0 pt-0">
                <span class="table-card-title">Weekly Sales</span>
                <span class="badge bg-light text-dark">Last 7 Days</span>
            </div>
            <canvas id="weeklyChart" height="130"></canvas>
        </div>
    </div>
    <div class="col-md-5">
        <div class="table-card p-3">
            <div class="table-card-header px-0 pt-0">
                <span class="table-card-title">Income vs Expense</span>
                <span class="badge bg-light text-dark">6 Months</span>
            </div>
            <canvas id="monthlyChart" height="130"></canvas>
        </div>
    </div>
</div>

{{-- Tables row --}}
<div class="row g-3">
    <div class="col-md-7">
        <div class="table-card">
            <div class="table-card-header">
                <span class="table-card-title">Recent Invoices</span>
                <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Client</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recent_invoices as $inv)
                        <tr>
                            <td><a href="{{ route('invoices.show',$inv) }}" class="text-primary fw-500">
                                    {{ $inv->invoice_no }}</a></td>
                            <td>{{ $inv->client?->name }}</td>
                            <td class="fw-600">{{ number_format($inv->total,2) }} SAR</td>
                            <td><span class="badge status-{{ $inv->status }} px-2 py-1 rounded-pill">
                                    {{ ucfirst($inv->status) }}</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No invoices yet</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-5">
        <div class="table-card">
            <div class="table-card-header">
                <span class="table-card-title">
                    <i class="bi bi-exclamation-triangle-fill text-warning me-1"></i> Low Stock Alert
                </span>
                <a href="{{ route('products.index',['low_stock'=>1]) }}" class="btn btn-sm btn-outline-warning">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Stock</th>
                            <th>Alert</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($low_stock as $p)
                        <tr>
                            <td>{{ $p->name }}</td>
                            <td><span class="text-danger fw-600">{{ $p->stock }}</span></td>
                            <td class="text-muted">{{ $p->alert_qty }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">
                                <i class="bi bi-check-circle-fill text-success me-1"></i>All stock OK
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    const weekLabels = @json($weekly_sales->pluck('date'));
    const weekData   = @json($weekly_sales->pluck('total'));

    const moLabels   = @json($monthly_chart->pluck('month'));
    const moIncome   = @json($monthly_chart->pluck('income'));
    const moExpense  = @json($monthly_chart->pluck('expense'));

    new Chart(document.getElementById('weeklyChart'), {
        type: 'bar',
        data: {
            labels: weekLabels,
            datasets: [{
                label: 'Sales (SAR)',
                data: weekData,
                backgroundColor: 'rgba(37,99,235,0.7)',
                borderRadius: 6,
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { font: { size: 11 } } },
                x: { ticks: { font: { size: 11 } } }
            }
        }
    });

    new Chart(document.getElementById('monthlyChart'), {
        type: 'line',
        data: {
            labels: moLabels,
            datasets: [
                {
                    label: 'Income',
                    data: moIncome,
                    borderColor: '#22c55e',
                    backgroundColor: 'rgba(34,197,94,.1)',
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Expense',
                    data: moExpense,
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239,68,68,.1)',
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            plugins: {
                legend: { position: 'top', labels: { font: { size: 11 } } }
            },
            scales: {
                y: { beginAtZero: true, ticks: { font: { size: 11 } } },
                x: { ticks: { font: { size: 11 } } }
            }
        }
    });
</script>
@endpush