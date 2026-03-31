@extends('layouts.app')

@section('title','Quotations')

@push('styles')
<style>
    .q-card {
        background: #fff;
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        padding: 16px;
        transition: .15s;
    }

    .q-card:hover {
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
        transform: translateY(-2px);
    }

    .q-head {
        font-weight: 700;
        font-size: 14px;
    }

    .q-meta {
        font-size: 12px;
        color: #6b7280;
    }

    .q-status {
        font-size: 11px;
        padding: 3px 8px;
        border-radius: 20px;
    }

    .status-draft {
        background: #e5e7eb;
    }

    .status-sent {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .status-converted {
        background: #dcfce7;
        color: #166534;
    }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>📄 Quotations</h4>

    <a href="{{ route('quotations.create') }}" class="btn btn-primary">
        + New Quotation
    </a>
</div>

{{-- 🔍 Filter --}}
<form method="GET" class="row mb-3">
    <div class="col-md-3">
        <input type="text" name="search" value="{{ request('search') }}"
            class="form-control" placeholder="Search quotation no...">
    </div>

    <div class="col-md-2">
        <select name="status" class="form-select">
            <option value="">All Status</option>
            <option value="draft" {{ request('status')=='draft'?'selected':'' }}>Draft</option>
            <option value="sent" {{ request('status')=='sent'?'selected':'' }}>Sent</option>
            <option value="converted" {{ request('status')=='converted'?'selected':'' }}>Converted</option>
        </select>
    </div>

    <div class="col-md-2">
        <button class="btn btn-dark w-100">Filter</button>
    </div>
</form>

{{-- 📦 Cards --}}
<div class="row g-3">

    @forelse($quotations as $q)
    <div class="col-md-4">
        <div class="q-card">

            <div class="d-flex justify-content-between">
                <div class="q-head">
                    #{{ $q->quotation_no }}
                </div>

                <span class="q-status status-{{ $q->status }}">
                    {{ ucfirst($q->status) }}
                </span>
            </div>

            <div class="q-meta mt-1">
                {{ $q->client->name ?? 'No client' }}
            </div>

            <div class="q-meta">
                {{ $q->quotation_date }}
            </div>

            <div class="mt-2 fw-bold">
                {{ number_format($q->total,2) }} SAR
            </div>

            <div class="mt-3 d-flex gap-2">

                <a href="{{ route('quotations.show',$q->id) }}"
                    class="btn btn-sm btn-outline-dark w-100">
                    View
                </a>

                <a href="{{ route('quotations.edit',$q->id) }}"
                    class="btn btn-sm btn-outline-primary w-100">
                    Edit
                </a>

            </div>

            {{-- Convert button --}}
            @if($q->status !== 'converted')
            <form method="POST"
                action="{{ route('quotations.convert',$q->id) }}"
                class="mt-2">
                @csrf
                <button class="btn btn-success btn-sm w-100">
                    Convert to Invoice
                </button>
            </form>
            @endif

        </div>
    </div>
    @empty

    <div class="text-center py-5 text-muted">
        No quotations found
    </div>

    @endforelse

</div>

{{-- Pagination --}}
<div class="mt-4">
    {{ $quotations->links() }}
</div>

@endsection