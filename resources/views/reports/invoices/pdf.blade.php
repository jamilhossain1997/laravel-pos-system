<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice Report</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        th {
            background: #f2f2f2;
        }
    </style>
</head>

<body>

    <h2>{{ $settings['company_name'] ?? 'Company' }}</h2>
    <p>{{ $settings['company_address'] ?? '' }}</p>

    <h3>Invoice Report</h3>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Client</th>
                <th>Status</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $index => $invoice)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $invoice->invoice_date }}</td>
                <td>{{ $invoice->client->name ?? '-' }}</td>
                <td>{{ $invoice->status }}</td>
                <td>{{ $invoice->grand_total }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>