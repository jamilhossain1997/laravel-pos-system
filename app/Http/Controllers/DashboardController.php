<?php

namespace App\Http\Controllers;
use App\Models\{Invoice, Product, Client, Expense, Income};
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

 
class DashboardController extends Controller {
    public function index() {
        $today  = Carbon::today();
        $month  = Carbon::now()->startOfMonth();
 
        $data = [
            'today_sales'    => Invoice::whereDate('invoice_date', $today)->sum('total'),
            'today_invoices' => Invoice::whereDate('invoice_date', $today)->count(),
            'month_sales'    => Invoice::where('invoice_date','>=',$month)->sum('total'),
            'month_profit'   => Invoice::where('invoice_date','>=',$month)->sum('total')
                                 - Expense::where('expense_date','>=',$month)->sum('amount'),
            'total_clients'  => Client::count(),
            'total_products' => Product::count(),
            'total_expense'  => Expense::where('expense_date','>=',$month)->sum('amount'),
            'total_income'   => Income::where('income_date','>=',$month)->sum('amount'),
 
            'recent_invoices' => Invoice::with('client')->latest()->take(8)->get(),
            'low_stock'       => Product::where('stock','<=',DB::raw('alert_qty'))->take(8)->get(),
 
            // Weekly sales for chart (last 7 days)
            'weekly_sales'   => collect(range(6,0))->map(fn($d) => [
                'date'  => Carbon::today()->subDays($d)->format('D'),
                'total' => Invoice::whereDate('invoice_date', Carbon::today()->subDays($d))->sum('total'),
            ]),
            // Monthly income vs expense for chart
            'monthly_chart'  => collect(range(5,0))->map(fn($m) => [
                'month'   => Carbon::now()->subMonths($m)->format('M'),
                'income'  => Invoice::whereMonth('invoice_date', Carbon::now()->subMonths($m))->sum('total'),
                'expense' => Expense::whereMonth('expense_date', Carbon::now()->subMonths($m))->sum('amount'),
            ]),
        ];
        return view('dashboard.index', $data);
    }
}
