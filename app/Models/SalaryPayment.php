<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryPayment extends Model
{
    protected $fillable = [
        'salary_record_id', 'employee_name', 'position', 'whatsapp',
        'month', 'year', 'base_salary', 'salary_extra', 'total_sessions',
        'session_bonus', 'transport_allowance', 'total_compensation',
        'deductions', 'net_salary', 'paid_at',
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'total_sessions' => 'decimal:2',
        'session_bonus' => 'decimal:2',
        'transport_allowance' => 'decimal:2',
        'total_compensation' => 'decimal:2',
        'deductions' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'paid_at' => 'date',
    ];

    public function salaryRecord()
    {
        return $this->belongsTo(SalaryRecord::class);
    }
}
