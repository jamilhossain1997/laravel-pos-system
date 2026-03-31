<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $expenses = Expense::with('user')
            ->when($request->from, fn($q, $d) => $q->whereDate('expense_date', '>=', $d))
            ->when($request->to,   fn($q, $d) => $q->whereDate('expense_date', '<=', $d))
            ->latest()->paginate(20);
        $total = Expense::sum('amount');
        return view('account.expense.index', compact('expenses', 'total'));
    }
    public function create()
    {
        return view('account.expense.create');
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:200',
            'category'     => 'required|string',
            'amount'       => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'reference'    => 'nullable|string',
            'note'         => 'nullable|string',
        ]);
        $data['user_id'] = auth()->id();
        Expense::create($data);
        return redirect()->route('account.expense.index')->with('success', 'Expense added!');
    }
    public function destroy(Expense $expense)
    {
        $expense->delete();
        return back()->with('success', 'Deleted!');
    }
}
