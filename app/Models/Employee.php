<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Employee extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'whatsapp',
        'position', 'nip', 'bank_account', 'bank_name',
        'hire_date', 'is_active', 'notes',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function salaryRecords(): HasMany
    {
        return $this->hasMany(SalaryRecord::class, 'employee_name', 'name');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch(Builder $query, ?string $q = null): Builder
    {
        if (!$q) return $query;
        return $query->where(function ($q2) use ($q) {
            $q2->where('name', 'like', "%{$q}%")
                ->orWhere('nip', 'like', "%{$q}%")
                ->orWhere('position', 'like', "%{$q}%")
                ->orWhere('phone', 'like', "%{$q}%");
        });
    }
}
