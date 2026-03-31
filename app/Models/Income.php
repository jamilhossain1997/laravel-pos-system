<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Income extends Model
{
    protected $fillable = ['title', 'category', 'amount', 'income_date', 'reference', 'user_id', 'note'];
    protected $casts = ['income_date' => 'date', 'amount' => 'decimal:2'];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
