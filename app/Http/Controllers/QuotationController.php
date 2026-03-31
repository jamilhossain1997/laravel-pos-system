<?php 

namespace App\Http\Controllers;
use App\Models\{Quotation, Quotation_item, Client, Product, Setting};
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
 
class QuotationController extends Controller {
    public function index(Request $request) {
        $quotations = Quotation::with('client')
            ->when($request->search, fn($q,$s) => $q->where('quotation_no','like',"%$s%"))
            ->when($request->status, fn($q,$s) => $q->where('status',$s))
            ->latest()->paginate(20);
        return view('quotations.index', compact('quotations'));
    }
    public function create() {
        $clients  = Client::where('is_active',true)->orderBy('name')->get();
        $products = Product::where('is_active',true)->with('unit')->get();
        $taxRate  = Setting::get('tax_rate', 15);
        return view('quotations.create', compact('clients','products','taxRate'));
    }
    public function store(Request $request) {
        $request->validate(['client_id'=>'required','quotation_date'=>'required|date','items'=>'required|array|min:1']);
        $taxRate  = (float) ($request->tax_percent ?? Setting::get('tax_rate',15));
        $subtotal = collect($request->items)->sum(fn($i) => $i['unit_price']*$i['qty']-($i['discount']??0));
        $discount = $request->discount ?? 0;
        $tax      = round(($subtotal-$discount)*($taxRate/100),2);
        $total    = ($subtotal-$discount)+$tax;
 
        $quotation = Quotation::create([
            'quotation_no'   => Quotation::generateNumber(),
            'client_id'      => $request->client_id,
            'user_id'        => auth()->id(),
            'quotation_date' => $request->quotation_date,
            'valid_until'    => $request->valid_until,
            'subtotal'       => $subtotal,
            'discount'       => $discount,
            'tax'            => $tax,
            'tax_percent'    => $taxRate,
            'total'          => $total,
            'status'         => 'draft',
            'notes'          => $request->notes,
        ]);
        foreach ($request->items as $item) {
            Quotation_item::create([
                'quotation_id' => $quotation->id,
                'product_id'   => $item['product_id'],
                'product_name' => $item['product_name'],
                'unit_price'   => $item['unit_price'],
                'qty'          => $item['qty'],
                'discount'     => $item['discount']??0,
                'subtotal'     => $item['unit_price']*$item['qty']-($item['discount']??0),
            ]);
        }
        return redirect()->route('quotations.show',$quotation)->with('success','Quotation created!');
    }
    public function show(Quotation $quotation) {
        $quotation->load(['client','items.product','user']);
        $settings = Setting::whereIn('key',['company_name','company_phone','company_address','company_vat','currency_symbol'])->pluck('value','key');
        return view('quotations.show', compact('quotation','settings'));
    }
    public function pdf(Quotation $quotation) {
        $quotation->load(['client','items.product','user']);
        $settings = Setting::whereIn('key',['company_name','company_phone','company_address','company_vat','currency_symbol'])->pluck('value','key');
        $pdf = Pdf::loadView('quotations.pdf', compact('quotation','settings'))->setPaper('a4');
        return $pdf->download("Quotation-{$quotation->quotation_no}.pdf");
    }
    public function convertToInvoice(Quotation $quotation) {
        $invoice = $quotation->convertToInvoice();
        return redirect()->route('invoices.show',$invoice)->with('success','Quotation converted to Invoice!');
    }
}