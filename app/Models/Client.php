<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'phone', 'email', 'address', 'company', 'vat_number', 'balance', 'type', 'is_active'];
    protected $casts = ['is_active' => 'boolean', 'balance' => 'decimal:2'];

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class);
    }
    public function totalDue(): float
    {
        return $this->invoices()->where('status', '!=', 'paid')->sum('due');
    }
}
