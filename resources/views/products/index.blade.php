@extends('layouts.app')
@section('title','Products')
@section('content')
<div class="page-header">
    <div>
        <div class="page-title">Products</div>
        @if($lowStockCount > 0)
        <span class="badge bg-danger">{{ $lowStockCount }} low stock items</span>
        @endif
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('barcodes.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-upc me-1"></i> Barcodes
        </a>
        <a href="{{ route('products.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Add Product
        </a>
    </div>
</div>

<div class="table-card">
    <div class="table-card-header">
        <span class="table-card-title">All Products</span>
        <form class="d-flex gap-2" method="GET">
            <input type="text" name="search" class="form-control form-control-sm"
                placeholder="Name, SKU, barcode..." value="{{ request('search') }}" style="width:200px">
            <select name="category" class="form-select form-select-sm" style="width:150px">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                <option {{ request('category')==$cat?'selected':'' }}>{{ $cat }}</option>
                @endforeach
            </select>
            <label class="d-flex align-items-center gap-1">
                <input type="checkbox" name="low_stock" value="1" {{ request('low_stock')?'checked':'' }}>
                <span style="font-size:12px">Low Stock Only</span>
            </label>
            <button class="btn btn-sm btn-outline-primary">Filter</button>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Unit</th>
                    <th class="text-end">Buy Price</th>
                    <th class="text-end">Sell Price</th>
                    <th class="text-center">Stock</th>
                    <th class="text-center">Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $p)
                <tr>
                    <td class="text-muted font-monospace" style="font-size:11px">{{ $p->sku }}</td>
                    <td>
                        <div class="fw-600">{{ $p->name }}</div>
                        @if($p->barcode)<small class="text-muted">{{ $p->barcode }}</small>@endif
                    </td>
                    <td>{{ $p->category ?? '—' }}</td>
                    <td>{{ $p->unit?->short_name }}</td>
                    <td class="text-end">{{ number_format($p->buy_price,2) }}</td>
                    <td class="text-end fw-600">{{ number_format($p->sell_price,2) }}</td>
                    <td class="text-center">
                        <span class="badge {{ $p->isLowStock() ? 'bg-danger stock-alert' : 'bg-success bg-opacity-10 text-success' }} px-2">
                            {{ $p->stock }}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge {{ $p->is_active ? 'bg-success' : 'bg-secondary' }} bg-opacity-10 {{ $p->is_active ? 'text-success' : 'text-secondary' }}">
                            {{ $p->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('products.edit',$p) }}" class="btn btn-xs btn-outline-primary">
                                <i class="bi bi-pencil"></i></a>
                            <form method="POST" action="{{ route('products.destroy',$p) }}"
                                onsubmit="return confirm('Delete product?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-xs btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-muted py-5">No products found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($products->hasPages())
    <div class="p-3">{{ $products->withQueryString()->links() }}</div>
    @endif
</div>
@endsection