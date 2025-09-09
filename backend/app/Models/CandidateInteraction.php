<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidateInteraction extends Model
{
    protected $fillable = [
        'employer_id',
        'candidate_id',
        'job_id',
        'interaction_type',
        'status',
        'message',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the employer who made the interaction.
     */
    public function employer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    /**
     * Get the candidate who was interacted with.
     */
    public function candidate(): BelongsTo
    {
        return $this->belongsTo(User::class, 'candidate_id');
    }

    /**
     * Get the job related to this interaction.
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }
}
