<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalaryRecord extends Model
{
    protected $fillable = [
        'employee_name', 'position', 'phone', 'whatsapp',
        'salary_date', 'month', 'year',
        'base_salary', 'salary_extra',
        'total_sessions', 'session_bonus',
        'transport_allowance',
        'total_compensation',
        'deductions', 'net_salary',
        'paid', 'paid_at',
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'salary_extra' => 'decimal:2',
        'total_sessions' => 'decimal:2',
        'session_bonus' => 'decimal:2',
        'transport_allowance' => 'decimal:2',
        'total_compensation' => 'decimal:2',
        'deductions' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'paid' => 'boolean',
        'paid_at' => 'date',
        'salary_date' => 'date',
    ];

    public function employee(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_name', 'name');
    }

    public function payrollPayments(): HasMany
    {
        return $this->hasMany(\App\Models\SalaryPayment::class, 'salary_record_id');
    }

    public function calculateTotals(): void
    {
        $this->total_compensation = $this->base_salary
            + $this->salary_extra
            + $this->session_bonus;
        $this->net_salary = $this->total_compensation - $this->transport_allowance - $this->deductions;
        $this->save();
    }
}
