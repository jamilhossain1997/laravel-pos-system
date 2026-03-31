<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quotation extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'quotation_no',
        'client_id',
        'user_id',
        'quotation_date',
        'valid_until',
        'subtotal',
        'discount',
        'tax',
        'tax_percent',
        'total',
        'status',
        'notes'
    ];
    protected $casts = ['quotation_date' => 'date', 'valid_until' => 'date'];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function items(): HasMany
    {
        return $this->hasMany(Quotation_item::class);
    }

    public static function generateNumber(): string
    {
        $prefix = Setting::get('quotation_prefix', 'QT-');
        $last = self::latest()->first();
        $num = $last ? ((int) substr($last->quotation_no, strlen($prefix))) + 1 : 1;
        return $prefix . str_pad($num, 5, '0', STR_PAD_LEFT);
    }
    public function convertToInvoice(): Invoice
    {
        $invoice = Invoice::create([
            'invoice_no'   => Invoice::generateNumber(),
            'client_id'    => $this->client_id,
            'user_id'      => auth()->id(),
            'invoice_date' => now(),
            'subtotal'     => $this->subtotal,
            'discount'     => $this->discount,
            'tax'          => $this->tax,
            'tax_percent'  => $this->tax_percent,
            'total'        => $this->total,
            'due'          => $this->total,
            'status'       => 'draft',
        ]);
        foreach ($this->items as $item) {
            Invoice_item::create([
                'invoice_id'   => $invoice->id,
                'product_id'   => $item->product_id,
                'product_name' => $item->product_name,
                'unit_price'   => $item->unit_price,
                'qty'          => $item->qty,
                'discount'     => $item->discount,
                'subtotal'     => $item->subtotal,
            ]);
        }
        $this->update(['status' => 'converted']);
        return $invoice;
    }
}
