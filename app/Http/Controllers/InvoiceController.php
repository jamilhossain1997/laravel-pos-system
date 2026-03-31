<?php

namespace App\Http\Controllers;

use App\Models\{Invoice, Invoice_item, Client, Product, Setting};
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $invoices = Invoice::with('client')
            ->when($request->search, fn($q, $s) => $q->where('invoice_no', 'like', "%$s%"))
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->from,   fn($q, $d) => $q->whereDate('invoice_date', '>=', $d))
            ->when($request->to,     fn($q, $d) => $q->whereDate('invoice_date', '<=', $d))
            ->latest()->paginate(20);
        return view('invoices.index', compact('invoices'));
    }
    public function create()
    {
        $clients  = Client::where('is_active', true)->orderBy('name')->get();
        $products = Product::where('is_active', true)->with('unit')->get();
        $taxRate  = Setting::get('tax_rate', 15);
        return view('invoices.create', compact('clients', 'products', 'taxRate'));
    }
    public function store(Request $request)
    {
        // Similar to PosController::checkout but from invoice form
        $request->validate([
            'client_id'   => 'required|exists:clients,id',
            'invoice_date' => 'required|date',
            'items'       => 'required|array|min:1',
        ]);
        $taxRate  = (float) ($request->tax_percent ?? Setting::get('tax_rate', 15));
        $subtotal = collect($request->items)->sum(fn($i) => $i['unit_price'] * $i['qty'] - ($i['discount'] ?? 0));
        $discount = $request->discount ?? 0;
        $tax      = round(($subtotal - $discount) * ($taxRate / 100), 2);
        $total    = ($subtotal - $discount) + $tax;
        $paid     = $request->paid ?? 0;

        $invoice = Invoice::create([
            'invoice_no'   => Invoice::generateNumber(),
            'client_id'    => $request->client_id,
            'user_id'      => auth()->id(),
            'invoice_date' => $request->invoice_date,
            'due_date'     => $request->due_date,
            'subtotal'     => $subtotal,
            'discount'     => $discount,
            'tax'          => $tax,
            'tax_percent'  => $taxRate,
            'total'        => $total,
            'paid'         => $paid,
            'due'          => max(0, $total - $paid),
            'status'       => $paid >= $total ? 'paid' : ($paid > 0 ? 'partial' : 'draft'),
            'payment_method' => $request->payment_method ?? 'cash',
            'notes'        => $request->notes,
        ]);
        foreach ($request->items as $item) {
            Invoice_item::create([
                'invoice_id'   => $invoice->id,
                'product_id'   => $item['product_id'],
                'product_name' => $item['product_name'],
                'unit_price'   => $item['unit_price'],
                'qty'          => $item['qty'],
                'discount'     => $item['discount'] ?? 0,
                'subtotal'     => $item['unit_price'] * $item['qty'] - ($item['discount'] ?? 0),
            ]);
            Product::find($item['product_id'])?->decreaseStock($item['qty']);
        }
        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice created!');
    }
    public function show(Invoice $invoice)
    {
        $invoice->load(['client', 'items.product', 'user']);
        $settings = Setting::whereIn('key', ['company_name', 'company_phone', 'company_address', 'company_vat', 'currency_symbol'])->pluck('value', 'key');
        return view('invoices.show', compact('invoice', 'settings'));
    }
    public function pdf(Invoice $invoice)
    {
        $invoice->load(['client', 'items.product', 'user']);
        $settings = Setting::whereIn('key', ['company_name', 'company_phone', 'company_address', 'company_vat', 'currency_symbol'])->pluck('value', 'key');
        $pdf = Pdf::loadView('invoices.pdf', compact('invoice', 'settings'))
            ->setPaper('a4', 'portrait');
        return $pdf->download("Invoice-{$invoice->invoice_no}.pdf");
    }
    public function recordPayment(Request $request, Invoice $invoice)
    {
        $request->validate(['amount' => 'required|numeric|min:0.01']);
        $newPaid = $invoice->paid + $request->amount;
        $newDue  = max(0, $invoice->total - $newPaid);
        $invoice->update([
            'paid'   => $newPaid,
            'due'    => $newDue,
            'status' => $newDue <= 0 ? 'paid' : 'partial',
        ]);
        return back()->with('success', 'Payment recorded!');
    }
    public function cancel(Invoice $invoice)
    {
        $invoice->update(['status' => 'cancelled']);
        return back()->with('success', 'Invoice cancelled.');
    }
}
