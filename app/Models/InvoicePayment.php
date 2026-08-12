<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoicePayment extends Model
{
    protected $fillable = [
        'child_id', 'month', 'year', 'amount',
        'paid_date', 'is_paid', 'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_date' => 'date',
        'is_paid' => 'boolean',
    ];

    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
    }

    public function markAsPaid(): void
    {
        $this->update([
            'is_paid' => true,
            'paid_date' => now()->format('Y-m-d'),
        ]);
    }

    public function markAsUnpaid(): void
    {
        $this->update([
            'is_paid' => false,
            'paid_date' => null,
        ]);
    }

    public static function generateForChild(int $childId, int $month = null, int $year = null): InvoicePayment
    {
        $child = Child::findOrFail($childId);
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;
        $amount = $child->calculateInvoiceAmount($month, $year);

        $payment = static::updateOrCreate(
            ['child_id' => $childId, 'month' => $month, 'year' => $year],
            ['amount' => $amount]
        );

        return $payment;
    }
}
