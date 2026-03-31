@extends('layouts.app')
@section('title','POS — New Sale')
@push('styles')
<style>
    #pos-wrap {
        display: grid;
        grid-template-columns: 1fr 360px;
        gap: 16px;
        height: calc(100vh - 110px);
    }

    .pos-products {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .prod-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
        gap: 10px;
        overflow-y: auto;
        flex: 1;
    }

    .prod-card {
        background: #fff;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px;
        cursor: pointer;
        text-align: center;
        transition: all .15s;
    }

    .prod-card:hover {
        border-color: #2563eb;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, .15);
    }

    .prod-card.out-of-stock {
        opacity: .5;
        cursor: not-allowed;
    }

    .prod-emoji {
        font-size: 26px;
        margin-bottom: 4px;
    }

    .prod-name {
        font-size: 12px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .prod-price {
        font-size: 13px;
        font-weight: 700;
        color: #2563eb;
    }

    .prod-stock {
        font-size: 10px;
        color: #9ca3af;
    }

    /* Cart */
    .cart-panel {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .cart-head {
        padding: 14px 16px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 14px;
        font-weight: 700;
    }

    .cart-body {
        flex: 1;
        overflow-y: auto;
        padding: 8px;
    }

    .cart-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px;
        border-radius: 8px;
        border: 1px solid #f1f5f9;
        margin-bottom: 6px;
    }

    .cart-name {
        font-size: 12px;
        font-weight: 500;
        flex: 1;
    }

    .qty-ctrl {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .qty-btn {
        width: 22px;
        height: 22px;
        border-radius: 5px;
        border: 1px solid #d1d5db;
        background: none;
        cursor: pointer;
        font-size: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #374151;
    }

    .qty-btn:hover {
        background: #f1f5f9;
    }

    .cart-price {
        font-size: 12px;
        font-weight: 700;
        color: #2563eb;
        min-width: 60px;
        text-align: right;
    }

    .cart-foot {
        border-top: 1px solid #e2e8f0;
        padding: 14px 16px;
    }

    .cart-row {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        color: #6b7280;
        margin-bottom: 5px;
    }

    .cart-total-row {
        display: flex;
        justify-content: space-between;
        font-size: 16px;
        font-weight: 700;
        color: #1e293b;
        margin: 8px 0;
    }
</style>
@endpush
@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <div class="page-title">POS — Point of Sale</div>
    <div class="d-flex gap-2">
        <input type="text" id="barcode-scan" class="form-control" placeholder="📷 Scan barcode..." style="width:220px">
        <select id="category-filter" class="form-select" style="width:140px">
            <option value="">All Categories</option>
            @foreach($products->pluck('category')->unique()->filter() as $cat)
            <option>{{ $cat }}</option>
            @endforeach
        </select>
        <input type="text" id="prod-search" class="form-control" placeholder="Search product..." style="width:180px">
    </div>
</div>

<form method="POST" action="{{ route('pos.checkout') }}" id="pos-form">
    @csrf
    <div id="pos-wrap">
        {{-- Products grid --}}
        <div class="pos-products">
            <div class="prod-grid" id="prod-grid">
                @foreach($products as $p)
                <div class="prod-card {{ $p->stock <= 0 ? 'out-of-stock' : '' }}"
                    data-id="{{ $p->id }}" data-name="{{ $p->name }}"
                    data-price="{{ $p->sell_price }}" data-stock="{{ $p->stock }}"
                    data-cat="{{ $p->category }}"
                    onclick="addToCart(this)">
                    <div class="prod-emoji">📦</div>
                    <div class="prod-name" title="{{ $p->name }}">{{ $p->name }}</div>
                    <div class="prod-price">{{ number_format($p->sell_price,2) }} SAR</div>
                    <div class="prod-stock">Stock: {{ $p->stock }} {{ $p->unit?->short_name }}</div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Cart --}}
        <div class="cart-panel">
            <div class="cart-head">
                <i class="bi bi-cart3 me-2"></i>Cart
                <span id="cart-count" class="badge bg-primary ms-2">0</span>
            </div>

            {{-- Client select --}}
            <div class="px-3 pt-2">
                <select name="client_id" class="form-select form-select-sm" required>
                    <option value="">— Select Client —</option>
                    @foreach($clients as $c)
                    <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->phone }})</option>
                    @endforeach
                </select>
            </div>

            <div class="cart-body" id="cart-body">
                <div class="text-center text-muted py-5" id="cart-empty">
                    <i class="bi bi-cart-x fs-2 d-block mb-2"></i>Cart is empty
                </div>
            </div>
            <div id="cart-items-container" style="display:none"></div>

            <div class="cart-foot">
                <div class="cart-row"><span>Subtotal</span><span id="display-sub">0.00 SAR</span></div>
                <div class="cart-row">
                    <span>Discount</span>
                    <input type="number" name="discount" id="discount-input" value="0" min="0"
                        class="form-control form-control-sm text-end" style="width:90px" onchange="calcTotal()">
                </div>
                <div class="cart-row"><span>VAT (15%)</span><span id="display-tax">0.00 SAR</span></div>
                <div class="cart-total-row"><span>TOTAL</span><span id="display-total">0.00 SAR</span></div>

                <div class="mb-2">
                    <label class="form-label mb-1" style="font-size:11px">Payment Method</label>
                    <select name="payment_method" class="form-select form-select-sm">
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="bank">Bank Transfer</option>
                        <option value="online">Online</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label mb-1" style="font-size:11px">Amount Paid</label>
                    <input type="number" name="paid" id="paid-input" class="form-control form-control-sm"
                        placeholder="0.00" step="0.01" min="0" required>
                </div>
                <button type="submit" class="btn btn-success w-100 fw-600" id="checkout-btn" disabled>
                    <i class="bi bi-check-circle me-2"></i>Complete Sale
                </button>
            </div>
        </div>
    </div>
</form>
@endsection
@push('scripts')
<script>
    let cart = {};

    function addToCart(el) {
        if (el.classList.contains('out-of-stock')) return;

        let id = el.dataset.id;
        let name = el.dataset.name;
        let price = parseFloat(el.dataset.price);
        let stock = parseInt(el.dataset.stock);

        if (cart[id]) {
            if (cart[id].qty >= stock) return alert("Not enough stock");
            cart[id].qty++;
        } else {
            cart[id] = {
                id,
                name,
                price,
                stock,
                qty: 1
            };
        }
        renderCart();
    }

    function changeQty(id, type) {
        if (!cart[id]) return;

        if (type === 'inc') {
            if (cart[id].qty >= cart[id].stock) return alert("Stock limit reached");
            cart[id].qty++;
        } else {
            cart[id].qty--;
            if (cart[id].qty <= 0) delete cart[id];
        }

        renderCart();
    }

    function renderCart() {
        let body = document.getElementById('cart-body');
        let items = Object.values(cart);

        if (items.length === 0) {
            body.innerHTML = `
            <div class="text-center text-muted py-5">
                <i class="bi bi-cart-x fs-2 d-block mb-2"></i>Cart is empty
            </div>`;
            document.getElementById('checkout-btn').disabled = true;
            document.getElementById('cart-count').textContent = 0;
            calcTotal();
            return;
        }

        document.getElementById('checkout-btn').disabled = false;
        document.getElementById('cart-count').textContent =
            items.reduce((sum, item) => sum + item.qty, 0);

        let html = '';
        items.forEach((i, index) => {
            html += `
        <div class="cart-item">
            <div class="cart-name">${i.name}</div>
            <div class="qty-ctrl">
                <button type="button" class="qty-btn" onclick="changeQty('${i.id}','dec')">−</button>
                <span>${i.qty}</span>
                <button type="button" class="qty-btn" onclick="changeQty('${i.id}','inc')">+</button>
            </div>
            <div class="cart-price">${(i.qty * i.price).toFixed(2)}</div>

            <input type="hidden" name="items[${index}][id]" value="${i.id}">
            <input type="hidden" name="items[${index}][qty]" value="${i.qty}">
        </div>`;
        });

        body.innerHTML = html;
        calcTotal();
    }

    function calcTotal() {
        let items = Object.values(cart);
        let subtotal = items.reduce((s, i) => s + i.price * i.qty, 0);
        let discount = parseFloat(document.getElementById('discount-input').value) || 0;
        let tax = (subtotal - discount) * 0.15;
        let total = (subtotal - discount) + tax;

        document.getElementById('display-sub').innerText = subtotal.toFixed(2) + ' SAR';
        document.getElementById('display-tax').innerText = tax.toFixed(2) + ' SAR';
        document.getElementById('display-total').innerText = total.toFixed(2) + ' SAR';

        document.getElementById('paid-input').placeholder = total.toFixed(2);
    }
</script>
@endpush