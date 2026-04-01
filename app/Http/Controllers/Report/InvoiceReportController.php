<?php

namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;
use App\Models\{Invoice, Client, Setting};
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
 
class InvoiceReportController extends Controller {
    public function index(Request $request) {
        $query = Invoice::with('client')
            ->when($request->from,    fn($q,$d) => $q->whereDate('invoice_date','>=',$d))
            ->when($request->to,      fn($q,$d) => $q->whereDate('invoice_date','<=',$d))
            ->when($request->status,  fn($q,$s) => $q->where('status',$s))
            ->when($request->client,  fn($q,$c) => $q->where('client_id',$c));
 
        $invoices = $query->latest()->paginate(25)->withQueryString();
        $summary  = [
            'total_invoices' => $query->count(),
            'total_amount'   => $query->sum('total'),
            'total_paid'     => $query->sum('paid'),
            'total_due'      => $query->sum('due'),
        ];
        $clients = Client::orderBy('name')->get();
        return view('reports.invoice', compact('invoices','summary','clients'));
    }
    public function pdf(Request $request) {
        $invoices = Invoice::with('client')
            ->when($request->from,   fn($q,$d) => $q->whereDate('invoice_date','>=',$d))
            ->when($request->to,     fn($q,$d) => $q->whereDate('invoice_date','<=',$d))
            ->when($request->status, fn($q,$s) => $q->where('status',$s))
            ->latest()->get();
        $settings = Setting::whereIn('key',['company_name','company_address','currency_symbol'])->pluck('value','key');
        $pdf = Pdf::loadView('reports.invoices.pdf', compact('invoices','settings'))->setPaper('a4','landscape');
        return $pdf->download('Invoice-Report-'.now()->format('Ymd').'.pdf');
    }
}