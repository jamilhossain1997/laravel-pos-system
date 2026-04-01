@extends('layouts.app')

@section('title','New Quotation')

@push('styles')
<style>
    .q-wrap {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 16px;
    }

    .prod-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
        gap: 10px;
        max-height: 70vh;
        overflow-y: auto;
    }

    .prod-card {
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 10px;
        text-align: center;
        cursor: pointer;
        background: #fff;
        transition: 0.2s;
    }

    .prod-card:hover {
        border-color: #2563eb;
        transform: translateY(-2px);
    }

    .cart-box {
        background: #fff;
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        padding: 15px;
        display: flex;
        flex-direction: column;
        height: 100%;
        max-height: 70vh;
    }

    .cart-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        font-size: 13px;
    }

    .cart-item div {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .total-box {
        margin-top: auto;
    }
</style>
@endpush

@section('content')

<form method="POST" action="{{ route('quotations.store') }}">
    @csrf
    <div class="q-wrap">

        {{-- LEFT: Products --}}
        <div>
            <div class="mb-3">
                <input type="text" id="search" class="form-control" placeholder="Search product...">
            </div>
            <div class="prod-grid" id="products">
                @foreach($products as $p)
                <div class="prod-card"
                    data-id="{{ $p->id }}"
                    data-name="{{ $p->name }}"
                    data-price="{{ $p->sell_price }}"
                    onclick="addItem(this)">
                    <div>📦</div>
                    <div style="font-size:12px">{{ $p->name }}</div>
                    <strong>{{ number_format($p->sell_price,2) }}</strong>
                </div>
                @endforeach
            </div>
        </div>

        {{-- RIGHT: Cart --}}
        <div class="cart-box">

            {{-- Client --}}
            <select name="client_id" class="form-select mb-2" required>
                <option value="">Select Client</option>
                @foreach($clients as $c)
                <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
            </select>

            {{-- Dates --}}
            <div class="mb-2">
                <label class="form-label">Quotation Date</label>
                <input type="date" name="quotation_date" value="{{ date('Y-m-d') }}" class="form-control form-control-sm" required>
            </div>
            <div class="mb-2">
                <label class="form-label">Valid Until</label>
                <input type="date" name="valid_until" value="{{ date('Y-m-d', strtotime('+7 days')) }}" class="form-control form-control-sm">
            </div>

            {{-- Notes --}}
            <div class="mb-2">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control form-control-sm" rows="2"></textarea>
            </div>

            {{-- Cart items --}}
            <div id="cart-items"></div>

            {{-- Totals --}}
            <div class="total-box">

                <div class="d-flex justify-content-between">
                    <span>Subtotal</span>
                    <span id="sub">0.00</span>
                </div>

                <div class="d-flex justify-content-between mt-1">
                    <span>Discount</span>
                    <input type="number" name="discount" id="discount" value="0"
                        class="form-control form-control-sm text-end" style="width:80px" onchange="calc()">
                </div>

                <div class="d-flex justify-content-between mt-1">
                    <span>Tax ({{ $taxRate }}%)</span>
                    <span id="tax">0.00</span>
                </div>

                <hr>

                <div class="d-flex justify-content-between fw-bold">
                    <span>Total</span>
                    <span id="total">0.00</span>
                </div>

                <button class="btn btn-success w-100 mt-3">
                    Save Quotation
                </button>

            </div>

        </div>

    </div>
</form>

@endsection

@push('scripts')
<script>
    let cart = {};

    function addItem(el) {
        let id = el.dataset.id;
        if (cart[id]) {
            cart[id].qty++;
        } else {
            cart[id] = {
                id,
                name: el.dataset.name,
                price: parseFloat(el.dataset.price),
                qty: 1
            };
        }
        render();
    }

    function changeQty(id, type) {
        if (!cart[id]) return;
        if (type === 'inc') cart[id].qty++;
        else {
            cart[id].qty--;
            if (cart[id].qty <= 0) delete cart[id];
        }
        render();
    }

    function render() {
        let html = '';
        let index = 0;
        Object.values(cart).forEach(i => {
            html += `<div class="cart-item">
            <span>${i.name}</span>
            <div>
                <button type="button" onclick="changeQty('${i.id}','dec')">-</button>
                ${i.qty}
                <button type="button" onclick="changeQty('${i.id}','inc')">+</button>
            </div>
            <span>${(i.price*i.qty).toFixed(2)}</span>
            <input type="hidden" name="items[${index}][product_id]" value="${i.id}">
            <input type="hidden" name="items[${index}][qty]" value="${i.qty}">
            <input type="hidden" name="items[${index}][unit_price]" value="${i.price}">
            <input type="hidden" name="items[${index}][product_name]" value="${i.name}">
        </div>`;
            index++;
        });
        document.getElementById('cart-items').innerHTML = html;
        calc();
    }

    function calc() {
        let sub = 0;
        Object.values(cart).forEach(i => {
            sub += i.price * i.qty;
        });
        let discount = parseFloat(document.getElementById('discount').value) || 0;
        let taxRate = {{$taxRate}};
        let taxable = Math.max(sub - discount, 0);
        let tax = taxable * (taxRate / 100);
        let total = taxable + tax;
        document.getElementById('sub').innerText = sub.toFixed(2);
        document.getElementById('tax').innerText = tax.toFixed(2);
        document.getElementById('total').innerText = total.toFixed(2);
    }
</script>
@endpush