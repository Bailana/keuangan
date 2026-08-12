<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\LogsActivity;

class Wallet extends Model
{
    use LogsActivity;
    protected $fillable = ['name', 'slug', 'is_default', 'initial_balance', 'owner_name', 'account_number'];

    protected $casts = [
        'initial_balance' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function ($wallet) {
            if (empty($wallet->slug)) {
                $wallet->slug = \Illuminate\Support\Str::slug($wallet->name);
            }
            if (!isset($wallet->initial_balance)) {
                $wallet->initial_balance = 0;
            }
        });
    }

    public function walletBalances(): HasMany
    {
        return $this->hasMany(WalletBalance::class);
    }

    public function getCurrentBalance(): float
    {
        $totalIncome = $this->incomes()->sum('amount');
        $totalExpense = $this->expenses()->sum('amount');

        return $this->initial_balance + $totalIncome - $totalExpense;
    }

    public function incomes()
    {
        return $this->hasMany(\App\Models\Income::class, 'wallet_id');
    }

    public function expenses()
    {
        return $this->hasMany(\App\Models\Expense::class, 'wallet_id');
    }

    public static function boot(): void
    {
        parent::boot();
        static::created(function ($wallet) {
            $wallet->walletBalances()->create([
                'balance' => $wallet->initial_balance,
                'month' => now()->startOfMonth(),
                'note' => 'Saldo awal',
            ]);
        });
    }

    public function scopeDefaultWallet(\Illuminate\Database\Eloquent\Builder $query)
    {
        return $query->where('is_default', true)->first();
    }
}
