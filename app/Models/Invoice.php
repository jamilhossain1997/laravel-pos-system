<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'invoice_no',
        'client_id',
        'user_id',
        'invoice_date',
        'due_date',
        'subtotal',
        'discount',
        'discount_percent',
        'tax',
        'tax_percent',
        'total',
        'paid',
        'due',
        'status',
        'payment_method',
        'notes'
    ];
    protected $casts = ['invoice_date' => 'date', 'due_date' => 'date'];

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
        return $this->hasMany(Invoice_item::class);
    }

    public static function generateNumber(): string
    {
        $prefix = Setting::get('invoice_prefix', 'INV-');
        $last = self::latest()->first();
        $num = $last ? ((int) substr($last->invoice_no, strlen($prefix))) + 1 : 1;
        return $prefix . str_pad($num, 5, '0', STR_PAD_LEFT);
    }
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }
    public function statusBadge(): string
    {
        return match ($this->status) {
            'paid'      => 'success',
            'partial'   => 'warning',
            'overdue'   => 'danger',
            'sent'      => 'info',
            'cancelled' => 'secondary',
            default     => 'light',
        };
    }
}
