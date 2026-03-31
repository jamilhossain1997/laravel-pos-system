<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index()
    {
        $units = Unit::withCount('products')->get();
        return view('admin.units.index', compact('units'));
    }
    public function store(Request $request)
    {
        $request->validate(['name' => 'required', 'short_name' => 'required|unique:units']);
        Unit::create($request->only('name', 'short_name'));
        return back()->with('success', 'Unit added!');
    }
    public function destroy(Unit $unit)
    {
        if ($unit->products()->exists()) return back()->withErrors(['error' => 'Unit in use by products.']);
        $unit->delete();
        return back()->with('success', 'Unit deleted!');
    }
}
