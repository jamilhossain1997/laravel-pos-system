<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Quotation_item extends Model
{
    protected $fillable = ['quotation_id', 'product_id', 'product_name', 'unit_price', 'qty', 'discount', 'subtotal'];
    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
