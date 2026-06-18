<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OptionSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_id',
        'department_id',
        'section_id',
    ];

    // ==================== RELATIONSHIPS ====================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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

    public function items(): HasMany
    {
        return $this->hasMany(OptionSubmissionItem::class, 'submission_id');
    }

    public function options()
    {
        return $this->belongsToMany(Option::class, 'option_submission_items', 'submission_id', 'option_id');
    }

    // ==================== HELPERS ====================

    /**
     * True when the user submitted with no options selected.
     */
    public function isNoSelection(): bool
    {
        return $this->items()->count() === 0;
    }

    // ==================== SCOPES ====================

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
}
