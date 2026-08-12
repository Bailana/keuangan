<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\LogsActivity;

class Expense extends Model
{
    use LogsActivity;
    protected $fillable = [
        'expense_category_id', 'title', 'date', 'amount',
        'bank_or_wallet', 'recipient', 'receipt_url', 'notes',
        'wallet_id',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(\App\Models\ExpenseCategory::class, 'expense_category_id');
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Wallet::class);
    }
}
