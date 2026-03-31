<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice_item extends Model
{
    protected $fillable = ['invoice_id', 'product_id', 'product_name', 'unit_price', 'qty', 'discount', 'subtotal'];
    protected $casts = ['unit_price' => 'decimal:2', 'discount' => 'decimal:2', 'subtotal' => 'decimal:2'];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
