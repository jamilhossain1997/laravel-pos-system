<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Barcode extends Model
{
    protected $fillable = ['product_id', 'barcode_no', 'type', 'print_qty'];
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
