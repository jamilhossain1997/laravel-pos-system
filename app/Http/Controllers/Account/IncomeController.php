<?php
namespace App\Http\Controllers\Account;
use App\Http\Controllers\Controller;
use App\Models\{Income, Expense};
use Illuminate\Http\Request;
 
class IncomeController extends Controller {
    public function index(Request $request) {
        $incomes = Income::with('user')
            ->when($request->from, fn($q,$d) => $q->whereDate('income_date','>=',$d))
            ->when($request->to,   fn($q,$d) => $q->whereDate('income_date','<=',$d))
            ->latest()->paginate(20);
        $total = Income::sum('amount');
        return view('account.income.index', compact('incomes','total'));
    }
    public function create() { return view('account.income.create'); }
    public function store(Request $request) {
        $data = $request->validate([
            'title'       => 'required|string|max:200',
            'category'    => 'required|string',
            'amount'      => 'required|numeric|min:0.01',
            'income_date' => 'required|date',
            'reference'   => 'nullable|string',
            'note'        => 'nullable|string',
        ]);
        $data['user_id'] = auth()->id();
        Income::create($data);
        return redirect()->route('account.income.index')->with('success','Income added!');
    }
    public function destroy(Income $income) {
        $income->delete();
        return back()->with('success','Deleted!');
    }
    public function summary() {
        $incomes  = Income::selectRaw('MONTH(income_date) m, SUM(amount) total')->groupBy('m')->pluck('total','m');
        $expenses = Expense::selectRaw('MONTH(expense_date) m, SUM(amount) total')->groupBy('m')->pluck('total','m');
        return view('account.summary', compact('incomes','expenses'));
    }
}