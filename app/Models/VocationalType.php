<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class VocationalType extends Model
{
    protected $fillable = ['name', 'price_per_session', 'notes'];

    protected $casts = [
        'price_per_session' => 'decimal:2',
    ];

    public function children(): BelongsToMany
    {
        return $this->belongsToMany(Child::class, 'child_vocational_types')
            ->withPivot('monthly_sessions')
            ->withTimestamps();
    }

    public function getAbbreviationAttribute(): string
    {
        return strtoupper(substr($this->name, 0, 2));
    }
}
