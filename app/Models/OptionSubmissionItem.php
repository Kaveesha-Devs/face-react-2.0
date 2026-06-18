<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OptionSubmissionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'option_id',
    ];

    // ==================== RELATIONSHIPS ====================

    public function submission(): BelongsTo
    {
        return $this->belongsTo(OptionSubmission::class, 'submission_id');
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(Option::class);
    }
}
