@extends('layouts.app')
@section('title','Income')
@section('content')
<div class="page-header">
    <div>
        <div class="page-title">Income</div>
        <div class="text-muted mt-1">Total: <strong class="text-success">{{ number_format($total,2) }} SAR</strong></div>
    </div>
    <a href="{{ route('account.income.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Add Income
    </a>
</div>

<div class="table-card">
    <div class="table-card-header">
        <span class="table-card-title">Income Records</span>
        <form class="d-flex gap-2" method="GET">
            <input type="date" name="from" class="form-control form-control-sm" value="{{ request('from') }}">
            <span class="align-self-center text-muted">to</span>
            <input type="date" name="to" class="form-control form-control-sm" value="{{ request('to') }}">
            <button class="btn btn-sm btn-outline-primary">Filter</button>
        </form>
    </div>
    <table class="table mb-0">
        <thead>
            <tr>
                <th>Title</th>
                <th>Category</th>
                <th>Date</th>
                <th class="text-end">Amount</th>
                <th>Ref</th>
                <th>By</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($incomes as $inc)
            <tr>
                <td class="fw-500">{{ $inc->title }}</td>
                <td><span class="badge bg-success bg-opacity-10 text-success">{{ $inc->category }}</span></td>
                <td>{{ $inc->income_date->format('d M Y') }}</td>
                <td class="text-end fw-700 text-success">{{ number_format($inc->amount,2) }} SAR</td>
                <td class="text-muted font-monospace" style="font-size:11px">{{ $inc->reference ?? '—' }}</td>
                <td class="text-muted">{{ $inc->user?->name }}</td>
                <td>
                    <form method="POST" action="{{ route('account.income.destroy',$inc) }}"
                        onsubmit="return confirm('Delete this record?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-xs btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center text-muted py-5">No income records found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($incomes->hasPages())
    <div class="p-3">{{ $incomes->withQueryString()->links() }}</div>
    @endif
</div>
@endsection