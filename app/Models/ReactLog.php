<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReactLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'react_type_id',
        'company_id',
        'department_id',
        'section_id',
        'note',
        'ip_address',
        'device_info',
    ];

    // ==================== RELATIONSHIPS ====================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reactType(): BelongsTo
    {
        return $this->belongsTo(ReactType::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    // ==================== SCOPES ====================

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeByCompany($query, ?int $companyId)
    {
        if ($companyId) {
            return $query->where('company_id', $companyId);
        }
        return $query;
    }

    public function scopeByDepartment($query, ?int $departmentId)
    {
        if ($departmentId) {
            return $query->where('department_id', $departmentId);
        }
        return $query;
    }

    public function scopeBySection($query, ?int $sectionId)
    {
        if ($sectionId) {
            return $query->where('section_id', $sectionId);
        }
        return $query;
    }

    public function scopeByDateRange($query, string $from, string $to)
    {
        return $query->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
    }

    public function scopeByHourRange($query, ?string $hourFrom, ?string $hourTo)
    {
        if ($hourFrom !== null && $hourTo !== null) {
            return $query->whereRaw('HOUR(created_at) BETWEEN ? AND ?', [(int)$hourFrom, (int)$hourTo]);
        }
        if ($hourFrom !== null) {
            return $query->whereRaw('HOUR(created_at) >= ?', [(int)$hourFrom]);
        }
        if ($hourTo !== null) {
            return $query->whereRaw('HOUR(created_at) <= ?', [(int)$hourTo]);
        }
        return $query;
    }
}