@extends('layouts.app')

@section('title','Receipt')

@section('content')
<div class="container py-4">

    <h3>🧾 Receipt</h3>
    <div class="d-flex justify-content-end mb-3 gap-2">
        <button onclick="window.print()" class="btn btn-dark">
            🖨️ Print
        </button>

        <a href="{{ route('invoices.pdf', $invoice->id) }}" class="btn btn-primary">
            ⬇️ Download PDF
        </a>
    </div>
    <div class="card p-3">
        <p><strong>Invoice ID:</strong> {{ $invoice->id ?? '' }}</p>
        <p><strong>Client:</strong> {{ $invoice->client->name ?? '' }}</p>

        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items ?? [] as $item)
                <tr>
                    <td>{{ $item->product->name ?? '' }}</td>
                    <td>{{ $item->qty }}</td>
                    <td>{{ $item->unit_price }}</td>
                    <td>{{ $item->qty * $item->unit_price }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <h5 class="text-end">
            Total: {{ $invoice->total ?? 0 }} SAR
        </h5>
    </div>

</div>
@endsection