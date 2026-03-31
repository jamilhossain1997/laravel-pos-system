@extends('layouts.app')
@section('title','Expenses')
@section('content')
<div class="page-header">
    <div>
        <div class="page-title">Expenses</div>
        <div class="text-muted mt-1">Total: <strong class="text-danger">{{ number_format($total,2) }} SAR</strong></div>
    </div>
    <a href="{{ route('account.expense.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Add Expense
    </a>
</div>
<div class="table-card">
    <div class="table-card-header">
        <span class="table-card-title">Expense Records</span>
        <form class="d-flex gap-2" method="GET">
            <input type="date" name="from" class="form-control form-control-sm" value="{{ request('from') }}">
            <span class="align-self-center text-muted">to</span>
            <input type="date" name="to" class="form-control form-control-sm" value="{{ request('to') }}">
            <button class="btn btn-sm btn-outline-primary">Filter</button>
        </form>
    </div>
    <table class="table mb-0">
        <thead><tr>
            <th>Title</th><th>Category</th><th>Date</th>
            <th class="text-end">Amount</th><th>Ref</th><th>By</th><th>Action</th>
        </tr></thead>
        <tbody>
            @forelse($expenses as $exp)
            <tr>
                <td class="fw-500">{{ $exp->title }}</td>
                <td><span class="badge bg-danger bg-opacity-10 text-danger">{{ $exp->category }}</span></td>
                <td>{{ $exp->expense_date->format('d M Y') }}</td>
                <td class="text-end fw-700 text-danger">{{ number_format($exp->amount,2) }} SAR</td>
                <td class="text-muted" style="font-size:11px">{{ $exp->reference ?? '—' }}</td>
                <td class="text-muted">{{ $exp->user?->name }}</td>
                <td>
                    <form method="POST" action="{{ route('account.expense.destroy',$exp) }}"
                          onsubmit="return confirm('Delete?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-xs btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center text-muted py-5">No expense records found</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($expenses->hasPages())
    <div class="p-3">{{ $expenses->withQueryString()->links() }}</div>
    @endif
</div>
@endsection