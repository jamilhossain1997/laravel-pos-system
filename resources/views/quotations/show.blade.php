@extends('layouts.app')

@section('title','Quotation')

@push('styles')
<style>
.quote-box {
    max-width: 900px;
    margin: auto;
    background: #fff;
    padding: 30px;
    border-radius: 10px;
    border: 1px solid #e5e7eb;
}

.quote-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 25px;
}

.quote-title {
    font-size: 22px;
    font-weight: 700;
}

.quote-meta {
    font-size: 13px;
    color: #6b7280;
}

.table th {
    background: #f9fafb;
    font-size: 12px;
}

.table td {
    font-size: 13px;
}

.total-box {
    width: 300px;
    margin-left: auto;
}

.print-hide {
    margin-bottom: 15px;
}

@media print {
    .print-hide {
        display: none;
    }
}
</style>
@endpush

@section('content')

<div class="print-hide d-flex justify-content-end gap-2">
    <button onclick="window.print()" class="btn btn-dark">🖨️ Print</button>
    <a href="{{ route('quotations.download',$quotation->id) }}" class="btn btn-primary">⬇️ PDF</a>
</div>

<div class="quote-box">

    {{-- Header --}}
    <div class="quote-header">
        <div>
            <div class="quote-title">Quotation</div>
            <div class="quote-meta">#{{ $quotation->quotation_no }}</div>
        </div>

        <div class="text-end quote-meta">
            <div>Date: {{ $quotation->quotation_date }}</div>
            <div>Valid Until: {{ $quotation->valid_until }}</div>
        </div>
    </div>

    {{-- Client --}}
    <div class="mb-4">
        <strong>Bill To:</strong><br>
        {{ $quotation->client->name }}<br>
        {{ $quotation->client->phone ?? '' }}
    </div>

    {{-- Items --}}
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Product</th>
                <th>Price</th>
                <th>Qty</th>
                <th>Discount</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quotation->items as $i => $item)
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $item->product_name }}</td>
                <td>{{ number_format($item->unit_price,2) }}</td>
                <td>{{ $item->qty }}</td>
                <td>{{ number_format($item->discount,2) }}</td>
                <td>{{ number_format($item->subtotal,2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Totals --}}
    <div class="total-box mt-3">
        <div class="d-flex justify-content-between">
            <span>Subtotal</span>
            <span>{{ number_format($quotation->subtotal,2) }}</span>
        </div>

        <div class="d-flex justify-content-between">
            <span>Discount</span>
            <span>{{ number_format($quotation->discount,2) }}</span>
        </div>

        <div class="d-flex justify-content-between">
            <span>Tax ({{ $quotation->tax_percent }}%)</span>
            <span>{{ number_format($quotation->tax,2) }}</span>
        </div>

        <hr>

        <div class="d-flex justify-content-between fw-bold">
            <span>Total</span>
            <span>{{ number_format($quotation->total,2) }}</span>
        </div>
    </div>

    {{-- Notes --}}
    @if($quotation->notes)
    <div class="mt-4">
        <strong>Notes:</strong>
        <p>{{ $quotation->notes }}</p>
    </div>
    @endif

    {{-- Footer --}}
    <div class="mt-5 text-center text-muted" style="font-size:12px">
        Thank you for your business 🙏
    </div>

</div>

@endsection