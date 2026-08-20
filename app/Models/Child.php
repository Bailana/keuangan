<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Child extends Model
{
    protected $fillable = [
        'name', 'is_active', 'parent_name', 'parent_whatsapp',
        'class_name', 'spp_fee', 'has_subsidi', 'subsidi_amount',
        'has_parent_support',
    ];

    protected $appends = ['current_invoice_amount', 'is_taking_sekolah', 'therapy_types_data', 'vocational_types_data'];

    protected $casts = [
        'is_active' => 'boolean',
        'has_subsidi' => 'boolean',
        'has_parent_support' => 'boolean',
        'subsidi_amount' => 'decimal:2',
        'spp_fee' => 'float',
    ];

    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function plans(): HasMany
    {
        return $this->hasMany(FinancialPlan::class);
    }

    public function invoicePayments(): HasMany
    {
        return $this->hasMany(InvoicePayment::class);
    }

    public function therapyTypes(): BelongsToMany
    {
        return $this->belongsToMany(TherapyType::class, 'child_therapy_types')
            ->withPivot('monthly_sessions')
            ->withTimestamps();
    }

    public function vocationalTypes(): BelongsToMany
    {
        return $this->belongsToMany(VocationalType::class, 'child_vocational_types')
            ->withPivot('monthly_sessions')
            ->withTimestamps();
    }

    public function isTakingSekolah(): bool
    {
        return !empty($this->class_name) || !empty($this->spp_fee);
    }

    public function isTakingTerapi(): bool
    {
        return $this->therapyTypes()->exists();
    }

    public function isTakingVokasi(): bool
    {
        return $this->vocationalTypes()->exists();
    }

    public function hasMultipleServices(): bool
    {
        $count = 0;
        if ($this->isTakingTerapi()) $count++;
        if ($this->isTakingSekolah()) $count++;
        if ($this->isTakingVokasi()) $count++;
        return $count > 1;
    }

    public function getServiceLabels(): array
    {
        $labels = [];
        if ($this->isTakingTerapi()) {
            $labels[] = 'Terapi';
        }
        if ($this->isTakingSekolah()) {
            $labels[] = 'Sekolah';
        }
        if ($this->isTakingVokasi()) {
            $labels[] = 'Vokasi';
        }
        return $labels;
    }

    public function getServiceBadges(): string
    {
        $labels = $this->getServiceLabels();
        if (empty($labels)) {
            return '<span class="text-xs text-gray-400">-</span>';
        }
        $html = '';
        foreach ($labels as $label) {
            $color = match ($label) {
                'Terapi' => 'bg-purple-100 text-purple-700',
                'Sekolah' => 'bg-blue-100 text-blue-700',
                'Vokasi' => 'bg-amber-100 text-amber-700',
                default => 'bg-gray-100 text-gray-700',
            };
            $html .= '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ' . $color . '">' . e($label) . '</span> ';
        }
        return $html;
    }

    public function getTherapyDetails(): string
    {
        $details = [];
        foreach ($this->therapyTypes as $therapy) {
            $sessions = $therapy->pivot->monthly_sessions ?? 4;
            $details[] = e($therapy->name) . ' (' . $sessions . ' sesi)';
        }
        return $details ? implode(', ', $details) : '-';
    }

    public function getTherapyTotal(): float
    {
        $total = 0;
        foreach ($this->therapyTypes as $therapy) {
            $sessions = $therapy->pivot->monthly_sessions ?? 4;
            $total += (float) $therapy->price_per_session * (int) $sessions;
        }
        return $total;
    }

    public function getVokasiDetails(): string
    {
        $details = [];
        foreach ($this->vocationalTypes as $vokasi) {
            $sessions = $vokasi->pivot->monthly_sessions ?? 4;
            $details[] = e($vokasi->name) . ' (' . $sessions . ' sesi)';
        }
        return $details ? implode(', ', $details) : '-';
    }

    public function calculateInvoiceAmount(int $month, int $year): float
    {
        $total = $this->calculateGrossAmount();
        $subsidi = $this->getSubsidiAmount();
        return max(0, $total - $subsidi);
    }

    public function calculateGrossAmount(): float
    {
        $total = 0;

        foreach ($this->therapyTypes as $therapy) {
            $sessions = $therapy->pivot->monthly_sessions ?? 4;
            $total += (float) $therapy->price_per_session * (int) $sessions;
        }

        foreach ($this->vocationalTypes as $vokasi) {
            $sessions = $vokasi->pivot->monthly_sessions ?? 4;
            $total += (float) $vokasi->price_per_session * (int) $sessions;
        }

        // Use child's SPP fee if set, otherwise use global config
        $schoolFee = $this->spp_fee ?? config('settings.school_fee', 0);
        if ($this->isTakingSekolah() && $schoolFee > 0) {
            $total += $schoolFee;
        }

        // Add parent support if enabled
        if ($this->has_parent_support) {
            $total += config('settings.parent_support_fee', 25000);
        }

        return $total;
    }

    public function getSubsidiAmount(): float
    {
        if (!$this->has_subsidi) {
            return 0;
        }
        return (float) ($this->subsidi_amount ?? 0);
    }

    public function getCurrentInvoiceAmount(): float
    {
        return $this->calculateInvoiceAmount(now()->month, now()->year);
    }

    public function getCurrentInvoicePayment(): ?InvoicePayment
    {
        return $this->invoicePayments()
            ->where('month', now()->month)
            ->where('year', now()->year)
            ->first();
    }

    public function isPaidThisMonth(): bool
    {
        $payment = $this->getCurrentInvoicePayment();
        if ($payment) {
            return $payment->is_paid;
        }

        $invoiceAmount = $this->getCurrentInvoiceAmount();
        if ($invoiceAmount <= 0) {
            return true;
        }

        $totalIncome = $this->incomes()
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('amount');

        return $totalIncome >= $invoiceAmount;
    }

    public function formatPhoneNumber(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        } elseif (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }
        return $phone;
    }

    public function getCurrentInvoiceAmountAttribute()
    {
        return $this->getCurrentInvoiceAmount();
    }

    public function getFormattedWhatsappAttribute(): string
    {
        if (!$this->parent_whatsapp) return '';
        return $this->formatPhoneNumber($this->parent_whatsapp);
    }

    public function getWhatsappUrlAttribute(): string
    {
        if (!$this->parent_whatsapp) return '';
        return 'https://wa.me/' . $this->formatPhoneNumber($this->parent_whatsapp);
    }

    public function getIsTakingSekolahAttribute(): bool
    {
        return $this->isTakingSekolah();
    }

    public function getTherapyTypesDataAttribute(): array
    {
        $therapyTypes = $this->getRelation('therapyTypes') ?? collect();

        return $therapyTypes->map(function ($therapy) {
            return [
                'id' => $therapy->id,
                'name' => $therapy->name,
                'price_per_session' => (float) $therapy->price_per_session,
                'pivot' => [
                    'monthly_sessions' => (int) ($therapy->pivot->monthly_sessions ?? 0),
                ],
            ];
        })->toArray();
    }

    public function getVocationalTypesDataAttribute(): array
    {
        $vocationalTypes = $this->getRelation('vocationalTypes') ?? collect();

        return $vocationalTypes->map(function ($vokasi) {
            return [
                'id' => $vokasi->id,
                'name' => $vokasi->name,
                'price_per_session' => (float) $vokasi->price_per_session,
                'pivot' => [
                    'monthly_sessions' => (int) ($vokasi->pivot->monthly_sessions ?? 0),
                ],
            ];
        })->toArray();
    }
}
