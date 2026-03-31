@extends('layouts.app')

@section('content')
<div class="container">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold">Create Invoice</h4>
        <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary">← Back</a>
    </div>

    <form action="{{ route('invoices.store') }}" method="POST">
        @csrf

        <div class="row">

            {{-- LEFT --}}
            <div class="col-md-8">
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-body">

                        {{-- Customer --}}
                        <div class="mb-3">
                            <label class="form-label">Customer</label>
                            <select name="customer_id" class="form-control" required>
                                <option value="">Select Customer</option>
                                @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Products Table --}}
                        <table class="table" id="invoiceTable">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th width="120">Qty</th>
                                    <th width="150">Price</th>
                                    <th width="150">Total</th>
                                    <th width="50"></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>

                        <button type="button" class="btn btn-sm btn-primary" onclick="addRow()">
                            + Add Product
                        </button>

                    </div>
                </div>
            </div>

            {{-- RIGHT --}}
            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">

                        <h5>Total Summary</h5>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <span id="subtotal">0</span>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Tax</span>
                            <input type="number" name="tax" id="tax" class="form-control form-control-sm w-50" value="0">
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between fw-bold">
                            <span>Total</span>
                            <span id="grandTotal">0</span>
                        </div>

                        <input type="hidden" name="total" id="totalInput">

                        <button class="btn btn-success w-100 mt-3">
                            Save Invoice
                        </button>

                    </div>
                </div>
            </div>

        </div>
    </form>

</div>

{{-- JS --}}
<script>
    let products = @json($products);

    function addRow() {
        let row = `
        <tr>
            <td>
                <select name="products[]" class="form-control" onchange="setPrice(this)">
                    <option value="">Select</option>
                    ${products.map(p => `<option value="${p.id}" data-price="${p.sell_price}">${p.name}</option>`).join('')}
                </select>
            </td>
            <td><input type="number" name="qty[]" value="1" class="form-control" oninput="calculate()"></td>
            <td><input type="number" name="price[]" class="form-control" oninput="calculate()"></td>
            <td class="rowTotal">0</td>
            <td><button type="button" onclick="removeRow(this)" class="btn btn-danger btn-sm">X</button></td>
        </tr>`;
        document.querySelector('#invoiceTable tbody').insertAdjacentHTML('beforeend', row);
    }

    function removeRow(btn) {
        btn.closest('tr').remove();
        calculate();
    }

    function setPrice(select) {
        let price = select.options[select.selectedIndex].dataset.price;
        let row = select.closest('tr');
        row.querySelector('input[name="price[]"]').value = price;
        calculate();
    }

    function calculate() {
        let subtotal = 0;

        document.querySelectorAll('#invoiceTable tbody tr').forEach(row => {
            let qty = row.querySelector('input[name="qty[]"]').value || 0;
            let price = row.querySelector('input[name="price[]"]').value || 0;

            let total = qty * price;
            row.querySelector('.rowTotal').innerText = total.toFixed(2);

            subtotal += total;
        });

        let tax = document.getElementById('tax').value || 0;
        let grand = subtotal + parseFloat(tax);

        document.getElementById('subtotal').innerText = subtotal.toFixed(2);
        document.getElementById('grandTotal').innerText = grand.toFixed(2);
        document.getElementById('totalInput').value = grand;
    }
</script>

@endsection