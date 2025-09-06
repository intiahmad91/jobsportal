<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class UserProfile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'phone',
        'bio',
        'avatar',
        'cv_path',
        'cover_letter',
        'user_type',
        'status',
        'location',
        'website',
        'linkedin',
        'github',
        'portfolio',
        'experience_level',
        'current_salary',
        'expected_salary',
        'employment_status',
        'open_to_work',
        'open_to_relocation',
        'open_to_remote',
        'preferred_job_types',
        'preferred_locations',
        'preferred_industries',
        'cv_path',
        'cv_original_name',
        'cv_size',
        'cv_mime_type',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'open_to_work' => 'boolean',
        'open_to_relocation' => 'boolean',
        'open_to_remote' => 'boolean',
        'preferred_job_types' => 'array',
        'preferred_locations' => 'array',
        'preferred_industries' => 'array',
    ];

    /**
     * Get the user that owns the profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the skills for the user profile.
     */
    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'user_skills', 'user_id', 'skill_id');
    }

    /**
     * Get the user's full name.
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Get the user's display name.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->full_name ?: $this->user->name;
    }

    /**
     * Check if the user is open to work.
     */
    public function isOpenToWork(): bool
    {
        return $this->open_to_work;
    }

    /**
     * Check if the user is open to relocation.
     */
    public function isOpenToRelocation(): bool
    {
        return $this->open_to_relocation;
    }

    /**
     * Check if the user is open to remote work.
     */
    public function isOpenToRemote(): bool
    {
        return $this->open_to_remote;
    }

    /**
     * Get the user's experience level as a readable string.
     */
    public function getExperienceLevelTextAttribute(): string
    {
        return match($this->experience_level) {
            'entry' => 'Entry Level',
            'junior' => 'Junior',
            'mid' => 'Mid Level',
            'senior' => 'Senior',
            'expert' => 'Expert',
            default => 'Not Specified'
        };
    }

    /**
     * Get the user's employment status as a readable string.
     */
    public function getEmploymentStatusTextAttribute(): string
    {
        return match($this->employment_status) {
            'employed' => 'Employed',
            'unemployed' => 'Unemployed',
            'freelancer' => 'Freelancer',
            'student' => 'Student',
            default => 'Not Specified'
        };
    }
}
