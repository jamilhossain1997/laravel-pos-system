<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            color: #1a1a1a;
        }

        .page {
            padding: 28px 32px;
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 24px;
        }

        .company-name {
            font-size: 22px;
            font-weight: 700;
            color: #1d4ed8;
        }

        .company-info {
            font-size: 11px;
            color: #6b7280;
            margin-top: 4px;
            line-height: 1.6;
        }

        .invoice-label {
            font-size: 28px;
            font-weight: 700;
            color: #111827;
            text-align: right;
        }

        .invoice-meta {
            font-size: 11px;
            color: #6b7280;
            text-align: right;
            margin-top: 4px;
            line-height: 1.6;
        }

        /* Divider */
        .divider {
            border: none;
            border-top: 2px solid #1d4ed8;
            margin: 16px 0;
        }

        /* Bill to */
        .bill-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .bill-box {
            flex: 1;
        }

        .bill-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #9ca3af;
            margin-bottom: 5px;
        }

        .bill-name {
            font-size: 13px;
            font-weight: 700;
            color: #111827;
        }

        .bill-info {
            font-size: 11px;
            color: #6b7280;
            line-height: 1.7;
        }

        .status-box {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 6px;
            padding: 8px 14px;
            text-align: center;
        }

        .status-label {
            font-size: 10px;
            color: #166534;
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .status-val {
            font-size: 15px;
            font-weight: 700;
            color: #166534;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        thead tr {
            background: #1d4ed8;
            color: #fff;
        }

        th {
            padding: 9px 12px;
            font-size: 11px;
            font-weight: 600;
            text-align: left;
        }

        th.text-right {
            text-align: right;
        }

        td {
            padding: 9px 12px;
            font-size: 12px;
            border-bottom: 1px solid #f3f4f6;
        }

        td.text-right {
            text-align: right;
        }

        tr:nth-child(even) td {
            background: #f9fafb;
        }

        /* Totals */
        .totals {
            margin-left: auto;
            width: 260px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            font-size: 12px;
            color: #6b7280;
            border-bottom: 1px solid #f3f4f6;
        }

        .total-final {
            display: flex;
            justify-content: space-between;
            padding: 9px 12px;
            background: #1d4ed8;
            color: #fff;
            border-radius: 6px;
            margin-top: 6px;
            font-size: 15px;
            font-weight: 700;
        }

        /* Footer */
        .footer {
            margin-top: 30px;
            padding-top: 14px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 11px;
            color: #9ca3af;
        }

        /* VAT note */
        .vat-note {
            margin-top: 16px;
            padding: 10px 14px;
            background: #eff6ff;
            border-left: 3px solid #2563eb;
            font-size: 11px;
            color: #1e40af;
            border-radius: 0 6px 6px 0;
        }
    </style>
</head>

<body>
    <div class="page">
        {{-- Header --}}
        <div class="header">
            <div>
                <div class="company-name">{{ $settings['company_name'] ?? 'My Store' }}</div>
                <div class="company-info">
                    {{ $settings['company_address'] ?? '' }}<br>
                    Tel: {{ $settings['company_phone'] ?? '' }}<br>
                    VAT No: {{ $settings['company_vat'] ?? '' }}
                </div>
            </div>
            <div>
                <div class="invoice-label">INVOICE</div>
                <div class="invoice-meta">
                    No: <strong>{{ $invoice->invoice_no }}</strong><br>
                    Date: {{ $invoice->invoice_date->format('d M Y') }}<br>
                    @if($invoice->due_date) Due: {{ $invoice->due_date->format('d M Y') }} @endif
                </div>
            </div>
        </div>
        <hr class="divider">

        {{-- Bill to + status --}}
        <div class="bill-row">
            <div class="bill-box">
                <div class="bill-label">Bill To</div>
                <div class="bill-name">{{ $invoice->client->name }}</div>
                <div class="bill-info">
                    {{ $invoice->client->phone }}<br>
                    {{ $invoice->client->email }}<br>
                    {{ $invoice->client->address }}<br>
                    @if($invoice->client->vat_number) VAT: {{ $invoice->client->vat_number }} @endif
                </div>
            </div>
            <div style="width:140px">
                <div class="status-box">
                    <div class="status-label">Status</div>
                    <div class="status-val">{{ strtoupper($invoice->status) }}</div>
                </div>
                <div class="bill-info text-right mt-2">
                    Prepared by: {{ $invoice->user?->name }}<br>
                    Payment: {{ ucfirst($invoice->payment_method) }}
                </div>
            </div>
        </div>

        {{-- Items table --}}
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th class="text-right">Unit Price</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Discount</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $i => $item)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ $item->product_name }}</td>
                    <td class="text-right">{{ number_format($item->unit_price,2) }}</td>
                    <td class="text-right">{{ $item->qty }}</td>
                    <td class="text-right">{{ number_format($item->discount,2) }}</td>
                    <td class="text-right"><strong>{{ number_format($item->subtotal,2) }}</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Totals --}}
        <div class="totals">
            <div class="total-row"><span>Subtotal</span><span>{{ number_format($invoice->subtotal,2) }} {{ $settings['currency_symbol']??'SAR' }}</span></div>
            <div class="total-row"><span>Discount</span><span>- {{ number_format($invoice->discount,2) }} {{ $settings['currency_symbol']??'SAR' }}</span></div>
            <div class="total-row"><span>VAT ({{ $invoice->tax_percent }}%)</span><span>{{ number_format($invoice->tax,2) }} {{ $settings['currency_symbol']??'SAR' }}</span></div>
            <div class="total-row"><span>Paid</span><span>{{ number_format($invoice->paid,2) }} {{ $settings['currency_symbol']??'SAR' }}</span></div>
            <div class="total-final">
                <span>TOTAL DUE</span>
                <span>{{ number_format($invoice->due,2) }} {{ $settings['currency_symbol']??'SAR' }}</span>
            </div>
        </div>

        @if($invoice->notes)
        <div class="vat-note mt-3"><strong>Note:</strong> {{ $invoice->notes }}</div>
        @endif

        <div class="footer">
            {{ $settings['company_name']??'My Store' }} — Thank you for your business! •
            This is a VAT invoice as per the KSA ZATCA regulations.
        </div>
    </div>
</body>

</html>