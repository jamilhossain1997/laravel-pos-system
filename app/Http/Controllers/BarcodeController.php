<?php
namespace App\Http\Controllers;
use App\Models\{Product, Barcode};
use Illuminate\Http\Request;
use Milon\Barcode\DNS1D;
use Milon\Barcode\DNS2D;
 
class BarcodeController extends Controller {
    public function index() {
        $products = Product::where('is_active',true)->get();
        $barcodes = Barcode::with('product')->latest()->paginate(20);
        return view('barcodes.index', compact('products','barcodes'));
    }
    public function generate(Request $request) {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'type'       => 'required|in:C128,C39,EAN13,EAN8,QRCODE',
            'print_qty'  => 'required|integer|min:1|max:100',
        ]);
        $product    = Product::findOrFail($request->product_id);
        $barcodeNo  = $product->barcode ?: $product->sku ?: 'P'.str_pad($product->id,6,'0',STR_PAD_LEFT);
 
        Barcode::create([
            'product_id'  => $product->id,
            'barcode_no'  => $barcodeNo,
            'type'        => $request->type,
            'print_qty'   => $request->print_qty,
        ]);
        return back()->with('success','Barcode generated!');
    }
    public function print($id) {
        $barcode = Barcode::with('product')->findOrFail($id);
        return view('barcodes.print', compact('barcode'));
    }
    public function lookup(Request $request) {
        $product = Product::where('barcode', $request->barcode)
            ->orWhere('sku', $request->barcode)
            ->with('unit')->first();
        if (!$product) return response()->json(['error'=>'Product not found'],404);
        return response()->json($product);
    }
}