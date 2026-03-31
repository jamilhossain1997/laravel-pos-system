@extends('layouts.app')
@section('title','Invoice '.$invoice->invoice_no)
@section('content')
<div class="page-header">
    <div>
        <div class="page-title">{{ $invoice->invoice_no }}</div>
        <span class="badge status-{{ $invoice->status }} px-3 py-2 rounded-pill">
            {{ strtoupper($invoice->status) }}
        </span>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('invoices.pdf',$invoice) }}" class="btn btn-outline-danger" target="_blank">
            <i class="bi bi-file-pdf me-1"></i> PDF
        </a>
        <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary">← Back</a>
    </div>
</div>

<div class="row g-4">
    {{-- Invoice details --}}
    <div class="col-md-8">
        <div class="form-card">
            <div class="d-flex justify-content-between mb-4">
                <div>
                    <div class="fw-700 fs-5">{{ $settings['company_name'] ?? '' }}</div>
                    <div class="text-muted" style="font-size:12px">{{ $settings['company_address'] ?? '' }}</div>
                    <div class="text-muted" style="font-size:12px">VAT: {{ $settings['company_vat'] ?? '' }}</div>
                </div>
                <div class="text-end">
                    <div class="fw-700 text-muted" style="font-size:11px;letter-spacing:.06em">INVOICE</div>
                    <div class="fw-700 fs-4 text-primary">{{ $invoice->invoice_no }}</div>
                    <div class="text-muted" style="font-size:12px">
                        Date: {{ $invoice->invoice_date->format('d M Y') }}<br>
                        @if($invoice->due_date) Due: {{ $invoice->due_date->format('d M Y') }} @endif
                    </div>
                </div>
            </div>

            {{-- Bill to --}}
            <div class="p-3 rounded mb-4" style="background:#f8fafc;border-left:3px solid #2563eb">
                <div class="text-muted" style="font-size:10px;text-transform:uppercase;letter-spacing:.07em">Bill To</div>
                <div class="fw-700 mt-1">{{ $invoice->client->name }}</div>
                <div class="text-muted" style="font-size:12px">{{ $invoice->client->phone }} · {{ $invoice->client->email }}</div>
                <div class="text-muted" style="font-size:12px">{{ $invoice->client->address }}</div>
            </div>

            {{-- Items --}}
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th class="text-end">Price</th>
                        <th class="text-end">Qty</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $i => $item)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td>{{ $item->product_name }}</td>
                        <td class="text-end">{{ number_format($item->unit_price,2) }}</td>
                        <td class="text-end">{{ $item->qty }}</td>
                        <td class="text-end fw-600">{{ number_format($item->subtotal,2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Totals --}}
            <div class="d-flex justify-content-end">
                <table style="width:260px;font-size:13px">
                    <tr>
                        <td class="text-muted">Subtotal</td>
                        <td class="text-end">{{ number_format($invoice->subtotal,2) }} SAR</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Discount</td>
                        <td class="text-end">- {{ number_format($invoice->discount,2) }} SAR</td>
                    </tr>
                    <tr>
                        <td class="text-muted">VAT ({{ $invoice->tax_percent }}%)</td>
                        <td class="text-end">{{ number_format($invoice->tax,2) }} SAR</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Paid</td>
                        <td class="text-end text-success">{{ number_format($invoice->paid,2) }} SAR</td>
                    </tr>
                    <tr style="border-top:2px solid #e2e8f0">
                        <td class="fw-700 py-2">TOTAL DUE</td>
                        <td class="text-end fw-700 text-primary py-2 fs-6">{{ number_format($invoice->due,2) }} SAR</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    {{-- Sidebar actions --}}
    <div class="col-md-4">
        {{-- Record payment --}}
        @if($invoice->due > 0)
        <div class="form-card mb-3">
            <h6 class="fw-700 mb-3">Record Payment</h6>
            <form method="POST" action="{{ route('invoices.pay',$invoice) }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Amount</label>
                    <input type="number" name="amount" class="form-control" step="0.01"
                        max="{{ $invoice->due }}" placeholder="{{ number_format($invoice->due,2) }}" required>
                </div>
                <button type="submit" class="btn btn-success w-100">
                    <i class="bi bi-cash me-1"></i> Record Payment
                </button>
            </form>
        </div>
        @endif

        {{-- Info card --}}
        <div class="form-card">
            <h6 class="fw-700 mb-3">Invoice Info</h6>
            <div class="d-flex justify-content-between py-2 border-bottom">
                <span class="text-muted">Created by</span>
                <span>{{ $invoice->user?->name }}</span>
            </div>
            <div class="d-flex justify-content-between py-2 border-bottom">
                <span class="text-muted">Payment</span>
                <span>{{ ucfirst($invoice->payment_method) }}</span>
            </div>
            <div class="d-flex justify-content-between py-2">
                <span class="text-muted">Items</span>
                <span>{{ $invoice->items->count() }}</span>
            </div>
            @if($invoice->status !== 'cancelled' && $invoice->status !== 'paid')
            <form method="POST" action="{{ route('invoices.cancel',$invoice) }}" class="mt-3"
                onsubmit="return confirm('Cancel this invoice?')">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                    <i class="bi bi-x-circle me-1"></i> Cancel Invoice
                </button>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection