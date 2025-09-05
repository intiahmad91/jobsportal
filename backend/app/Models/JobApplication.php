<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id',
        'user_id',
        'cover_letter',
        'cv_path',
        'portfolio_url',
        'linkedin_url',
        'github_url',
        'status',
        'employer_notes',
        'candidate_notes',
        'reviewed_at',
        'shortlisted_at',
        'interviewed_at',
        'offered_at',
        'rejected_at',
        'rejection_reason',
        'is_favorite',
        'rating',
    ];

    protected $casts = [
        'is_favorite' => 'boolean',
        'reviewed_at' => 'datetime',
        'shortlisted_at' => 'datetime',
        'interviewed_at' => 'datetime',
        'offered_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Pending Review',
            'reviewed' => 'Reviewed',
            'shortlisted' => 'Shortlisted',
            'interviewed' => 'Interviewed',
            'offered' => 'Job Offered',
            'rejected' => 'Rejected',
            'withdrawn' => 'Withdrawn',
            default => 'Unknown'
        };
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeShortlisted($query)
    {
        return $query->where('status', 'shortlisted');
    }
}
