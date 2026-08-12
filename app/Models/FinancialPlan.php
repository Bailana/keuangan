<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialPlan extends Model
{
    protected $fillable = [
        'title', 'type', 'category', 'month', 'year', 'target_amount', 'notes',
    ];

    protected $casts = [
        'target_amount' => 'integer',
        'month' => 'integer',
        'year' => 'integer',
    ];

    protected $attributes = [
        'target_amount' => 0,
    ];

}
