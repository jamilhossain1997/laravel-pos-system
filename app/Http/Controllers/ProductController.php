<?php

namespace App\Http\Controllers;

use App\Models\{Product, Unit};
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('unit')
            ->when($request->search, fn($q, $s) => $q->where('name', 'like', "%$s%")->orWhere('barcode', 'like', "%$s%")->orWhere('sku', 'like', "%$s%"))
            ->when($request->category, fn($q, $c) => $q->where('category', $c))
            ->when($request->low_stock, fn($q) => $q->whereColumn('stock', '<=', 'alert_qty'))
            ->latest()->paginate(25);
        $categories = Product::whereNotNull('category')->distinct()->pluck('category');
        $lowStockCount = Product::whereColumn('stock', '<=', 'alert_qty')->count();
        return view('products.index', compact('products', 'categories', 'lowStockCount'));
    }

    public function create()
    {
        $units = Unit::all();
        return view('products.create', compact('units'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:200',
            'sku'        => 'nullable|string|unique:products',
            'barcode'    => 'nullable|string',
            'unit_id'    => 'required|exists:units,id',
            'category'   => 'nullable|string',
            'buy_price'  => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'stock'      => 'required|integer|min:0',
            'alert_qty'  => 'required|integer|min:0',
            'description' => 'nullable|string',
            'is_active'  => 'boolean',
        ]);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }
        if (empty($data['sku'])) {
            $data['sku'] = 'PRD-' . str_pad(Product::count() + 1, 5, '0', STR_PAD_LEFT);
        }
        Product::create($data);
        return redirect()->route('products.index')->with('success', 'Product added!');
    }

    public function edit(Product $product)
    {
        $units = Unit::all();
        return view('products.edit', compact('product', 'units'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name'       => 'required',
            'unit_id' => 'required|exists:units,id',
            'buy_price'  => 'required|numeric',
            'sell_price' => 'required|numeric',
            'stock'      => 'required|integer',
            'alert_qty' => 'required|integer',
            'category'   => 'nullable',
            'description' => 'nullable',
            'is_active' => 'boolean',
        ]);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }
        $product->update($data);
        return redirect()->route('products.index')->with('success', 'Product updated!');
    }
    
    public function destroy(Product $product)
    {
        $product->delete();
        return back()->with('success', 'Product deleted!');
    }
}
