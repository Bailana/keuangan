<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\LogsActivity;

class Income extends Model
{
    use LogsActivity;
    protected $fillable = [
        'child_id', 'income_category_id', 'date', 'amount', 'bank_or_wallet', 'notes', 'sender_name',
        'wallet_id',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(\App\Models\IncomeCategory::class, 'income_category_id');
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Wallet::class);
    }
}
