@extends('layouts.app')
@section('title','Clients')
@section('content')
<div class="page-header">
    <div class="page-title">Clients</div>
    <a href="{{ route('clients.create') }}" class="btn btn-primary">
        <i class="bi bi-person-plus me-1"></i> Add Client
    </a>
</div>

<div class="table-card mb-3">
    <div class="table-card-header">
        <span class="table-card-title">All Clients</span>
        <form class="d-flex gap-2" method="GET">
            <input type="text" name="search" class="form-control form-control-sm"
                placeholder="Search name, phone, email..." value="{{ request('search') }}" style="width:220px">
            <select name="type" class="form-select form-select-sm" style="width:130px">
                <option value="">All Types</option>
                <option {{ request('type')=='retail'?'selected':'' }}>retail</option>
                <option {{ request('type')=='wholesale'?'selected':'' }}>wholesale</option>
            </select>
            <button class="btn btn-sm btn-outline-primary">Search</button>
            <a href="{{ route('clients.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Type</th>
                    <th>Invoices</th>
                    <th>Total Sales</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $c)
                <tr>
                    <td class="text-muted">{{ $c->id }}</td>
                    <td>
                        <div class="fw-600">{{ $c->name }}</div>
                        @if($c->company)<small class="text-muted">{{ $c->company }}</small>@endif
                    </td>
                    <td>{{ $c->phone ?? '—' }}</td>
                    <td>{{ $c->email ?? '—' }}</td>
                    <td><span class="badge {{ $c->type=='wholesale'?'bg-info':'bg-secondary' }} bg-opacity-10 text-dark px-2">
                            {{ ucfirst($c->type) }}</span></td>
                    <td class="text-center">{{ $c->invoices_count }}</td>
                    <td class="fw-600">{{ number_format($c->invoices_sum_total,2) }} SAR</td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('clients.ledger',$c) }}" class="btn btn-xs btn-outline-info" title="Ledger">
                                <i class="bi bi-journal-text"></i></a>
                            <a href="{{ route('clients.edit',$c) }}" class="btn btn-xs btn-outline-primary" title="Edit">
                                <i class="bi bi-pencil"></i></a>
                            <form method="POST" action="{{ route('clients.destroy',$c) }}" onsubmit="return confirm('Delete this client?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-xs btn-outline-danger" title="Delete">
                                    <i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-5">No clients found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($clients->hasPages())
    <div class="p-3">{{ $clients->withQueryString()->links() }}</div>
    @endif
</div>
@endsection