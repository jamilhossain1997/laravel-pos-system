<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'sku',
        'barcode',
        'unit_id',
        'category',
        'buy_price',
        'sell_price',
        'stock',
        'alert_qty',
        'description',
        'image',
        'is_active'
    ];

    protected $casts = [
        'buy_price'  => 'decimal:2',
        'sell_price' => 'decimal:2',
        'is_active'  => 'boolean',
        'stock'      => 'integer',
        'alert_qty'  => 'integer',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock', '<=', 'alert_qty');
    }

    public function isLowStock(): bool
    {
        return $this->stock <= $this->alert_qty;
    }

    public function decreaseStock($quantity)
    {
        $this->decrement('stock', $quantity);
    }
}