<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReactType extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'icon_code',
        'sinhala_type',
        'tamil_type',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function reactLogs(): HasMany
    {
        return $this->hasMany(ReactLog::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}