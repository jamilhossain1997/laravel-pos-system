@extends('layouts.app')
@section('title','Invoice Overview Report')
@section('content')
<div class="page-header">
    <div class="page-title">Invoice Overview Report</div>
    <div class="d-flex gap-2">
        <a href="{{ route('reports.invoices.pdf', request()->query()) }}" class="btn btn-outline-danger">
            <i class="bi bi-file-pdf me-1"></i> Export PDF
        </a>
    </div>
</div>

{{-- Filters --}}
<div class="form-card mb-4">
    <form method="GET" class="row g-3 align-items-end">
        <div class="col-md-2">
            <label class="form-label">From Date</label>
            <input type="date" name="from" class="form-control" value="{{ request('from') }}">
        </div>
        <div class="col-md-2">
            <label class="form-label">To Date</label>
            <input type="date" name="to" class="form-control" value="{{ request('to') }}">
        </div>
        <div class="col-md-2">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">All Status</option>
                @foreach(['draft','sent','paid','partial','overdue','cancelled'] as $s)
                <option {{ request('status')==$s?'selected':'' }}>{{ $s }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Client</label>
            <select name="client" class="form-select">
                <option value="">All Clients</option>
                @foreach($clients as $c)
                <option value="{{ $c->id }}" {{ request('client')==$c->id?'selected':'' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button class="btn btn-primary flex-fill">
                <i class="bi bi-search me-1"></i> Filter
            </button>
            <a href="{{ route('reports.invoices') }}" class="btn btn-outline-secondary">Clear</a>
        </div>
    </form>
</div>

{{-- Summary cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card text-center">
            <div class="stat-label">Total Invoices</div>
            <div class="stat-value text-primary">{{ number_format($summary['total_invoices']) }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card text-center">
            <div class="stat-label">Total Amount</div>
            <div class="stat-value text-dark">{{ number_format($summary['total_amount'],2) }} SAR</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card text-center">
            <div class="stat-label">Total Collected</div>
            <div class="stat-value text-success">{{ number_format($summary['total_paid'],2) }} SAR</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card text-center">
            <div class="stat-label">Total Due</div>
            <div class="stat-value text-danger">{{ number_format($summary['total_due'],2) }} SAR</div>
        </div>
    </div>
</div>

{{-- Table --}}
<div class="table-card">
    <div class="table-card-header">
        <span class="table-card-title">Invoice List</span>
        <span class="text-muted" style="font-size:12px">{{ $invoices->total() }} results</span>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Invoice #</th>
                    <th>Client</th>
                    <th>Date</th>
                    <th class="text-end">Total</th>
                    <th class="text-end">Paid</th>
                    <th class="text-end">Due</th>
                    <th>Status</th>
                    <th>Payment</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $inv)
                <tr>
                    <td><a href="{{ route('invoices.show',$inv) }}" class="text-primary">{{ $inv->invoice_no }}</a></td>
                    <td>{{ $inv->client?->name }}</td>
                    <td>{{ $inv->invoice_date->format('d M Y') }}</td>
                    <td class="text-end fw-600">{{ number_format($inv->total,2) }}</td>
                    <td class="text-end text-success">{{ number_format($inv->paid,2) }}</td>
                    <td class="text-end {{ $inv->due > 0 ? 'text-danger fw-600' : 'text-muted' }}">
                        {{ number_format($inv->due,2) }}
                    </td>
                    <td><span class="badge status-{{ $inv->status }} px-2 rounded-pill">{{ ucfirst($inv->status) }}</span></td>
                    <td class="text-muted">{{ ucfirst($inv->payment_method) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-5">No invoices match the filters</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($invoices->hasPages())
    <div class="p-3">{{ $invoices->withQueryString()->links() }}</div>
    @endif
</div>
@endsection