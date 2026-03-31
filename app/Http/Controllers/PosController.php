<?php

namespace App\Http\Controllers;

use App\Models\{Product, Client, Invoice, Invoice_item, Setting};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class PosController extends Controller
{
    public function index()
    {
        $clients  = Client::where('is_active', true)->orderBy('name')->get();
        $products = Product::where('is_active', true)->with('unit')->get();
        return view('pos.index', compact('clients', 'products'));
    }

    public function apiProducts(Request $request)
    {
        $q = $request->q ?? '';
        return Product::where('is_active', true)
            ->where(fn($qb) => $qb->where('name', 'like', "%$q%")
                ->orWhere('barcode', 'like', "%$q%")
                ->orWhere('sku', 'like', "%$q%"))
            ->with('unit')->take(50)->get();
    }

    public function apiProduct($id)
    {
        return Product::with('unit')->findOrFail($id);
    }

    public function apiClients(Request $request)
    {
        $q = $request->q ?? '';
        return Client::where('is_active', true)
            ->where(fn($qb) => $qb->where('name', 'like', "%$q%")
                ->orWhere('phone', 'like', "%$q%"))
            ->take(20)->get();
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'client_id'      => 'required|exists:clients,id',
            'items'          => 'required|array|min:1',
            'items.*.id'     => 'required|exists:products,id',
            'items.*.qty'    => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,card,bank,online',
            'paid'           => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $taxRate  = (float) Setting::get('tax_rate', 15);
            $subtotal = 0;
            $items    = [];

            foreach ($request->items as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['id']);
                if ($product->stock < $item['qty']) {
                    return back()->withErrors(['stock' => "Insufficient stock for {$product->name}"]);
                }
                $lineTotal = $product->sell_price * $item['qty'];
                $discount  = $item['discount'] ?? 0;
                $subtotal += ($lineTotal - $discount);
                $items[]   = compact('product', 'item', 'lineTotal', 'discount');
            }

            $discountTotal = $request->discount ?? 0;
            $taxable  = $subtotal - $discountTotal;
            $tax      = round($taxable * ($taxRate / 100), 2);
            $total    = $taxable + $tax;
            $paid     = (float) $request->paid;
            $due      = max(0, $total - $paid);

            $invoice = Invoice::create([
                'invoice_no'      => Invoice::generateNumber(),
                'client_id'       => $request->client_id,
                'user_id'         => auth()->id(),
                'invoice_date'    => now(),
                'subtotal'        => $subtotal,
                'discount'        => $discountTotal,
                'discount_percent' => $subtotal > 0 ? round($discountTotal / $subtotal * 100, 2) : 0,
                'tax'             => $tax,
                'tax_percent'     => $taxRate,
                'total'           => $total,
                'paid'            => $paid,
                'due'             => $due,
                'status'          => $due <= 0 ? 'paid' : 'partial',
                'payment_method'  => $request->payment_method,
                'notes'           => $request->notes,
            ]);

            foreach ($items as $i) {
                Invoice_item::create([
                    'invoice_id'   => $invoice->id,
                    'product_id'   => $i['product']->id,
                    'product_name' => $i['product']->name,
                    'unit_price'   => $i['product']->sell_price,
                    'qty'          => $i['item']['qty'],
                    'discount'     => $i['discount'],
                    'subtotal'     => $i['lineTotal'] - $i['discount'],
                ]);
                $i['product']->decreaseStock($i['item']['qty']);
            }

            DB::commit();
            return redirect()->route('pos.receipt', $invoice->id)
                ->with('success', 'Sale completed successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function receipt($id)
    {
        $invoice = Invoice::with(['client','items', 'items.product', 'user'])->findOrFail($id);
        return view('pos.receipt', compact('invoice'));
    }

    public function download($id)
    {
        $invoice = Invoice::with('items.product', 'client')->findOrFail($id);

        $pdf = Pdf::loadView('pos.receipt', compact('invoice'));

        return $pdf->download('invoice-' . $invoice->id . '.pdf');
    }
}
