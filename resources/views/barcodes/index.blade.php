@extends('layouts.app')
@section('title','Barcode Manager')
@section('content')
<div class="page-header">
    <div class="page-title">Barcode Manager</div>
    <a href="{{ route('barcodes.generate') }}" class="btn btn-outline-primary">
        <i class="bi bi-camera me-1"></i> Scanner
    </a>
</div>

<div class="row g-4">
    {{-- Generate form --}}
    <div class="col-md-4">
        <div class="form-card h-100">
            <h6 class="fw-700 mb-3">Generate Barcode</h6>
            <form method="POST" action="{{ route('barcodes.generate') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Product *</label>
                    <select name="product_id" class="form-select" required>
                        <option value="">— Select Product —</option>
                        @foreach($products as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->sku }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Barcode Type</label>
                    <select name="type" class="form-select">
                        <option value="C128">Code 128 (recommended)</option>
                        <option value="EAN13">EAN-13</option>
                        <option value="EAN8">EAN-8</option>
                        <option value="C39">Code 39</option>
                        <option value="QRCODE">QR Code</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="form-label">Print Quantity</label>
                    <input type="number" name="print_qty" class="form-control" value="1" min="1" max="100">
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-upc me-1"></i> Generate
                </button>
            </form>
        </div>
    </div>

    {{-- Barcode list --}}
    <div class="col-md-8">
        <div class="table-card">
            <div class="table-card-header">
                <span class="table-card-title">Generated Barcodes</span>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Barcode No</th>
                            <th>Type</th>
                            <th>Qty</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($barcodes as $b)
                        <tr>
                            <td>{{ $b->product?->name }}</td>
                            <td class="font-monospace">{{ $b->barcode_no }}</td>
                            <td><span class="badge bg-light text-dark">{{ $b->type }}</span></td>
                            <td>{{ $b->print_qty }}</td>
                            <td>
                                <a href="{{ route('barcodes.print',$b->id) }}" target="_blank"
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-printer me-1"></i> Print
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No barcodes yet</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($barcodes->hasPages())
            <div class="p-3">{{ $barcodes->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection