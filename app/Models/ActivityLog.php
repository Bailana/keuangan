<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'subject_type',
        'subject_id',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'location',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->morphTo();
    }

    /**
     * Get the formatted action label.
     */
    public function getActionLabelAttribute(): string
    {
        return match($this->action) {
            'create' => 'Dibuat',
            'update' => 'Diperbarui',
            'delete' => 'Dihapus',
            'login' => 'Login',
            'logout' => 'Logout',
            'export_pdf' => 'Export PDF',
            'export_excel' => 'Export Excel',
            default => $this->action,
        };
    }

    /**
     * Get the subject display name.
     */
    public function getSubjectLabelAttribute(): string
    {
        return match($this->subject_type) {
            Income::class => 'Pemasukan',
            Expense::class => 'Pengeluaran',
            Wallet::class => 'Dompet',
            default => class_basename($this->subject_type) ?: 'Item',
        };
    }

    /**
     * Get the action color based on type.
     */
    public function getActionColorAttribute(): string
    {
        return match($this->action) {
            'create' => 'text-emerald-600 bg-emerald-50',
            'update' => 'text-blue-600 bg-blue-50',
            'delete' => 'text-red-600 bg-red-50',
            'login' => 'text-purple-600 bg-purple-50',
            'logout' => 'text-gray-600 bg-gray-50',
            'export_pdf' => 'text-orange-600 bg-orange-50',
            'export_excel' => 'text-green-600 bg-green-50',
            default => 'text-gray-600 bg-gray-50',
        };
    }
}
