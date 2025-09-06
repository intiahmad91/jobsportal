<?php

namespace App\Models;

use App\Models\Category;
use App\Models\Skill;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Job extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'user_id',
        'title',
        'description',
        'requirements',
        'responsibilities',
        'benefits',
        'category_id',
        'location',
        'job_type',
        'experience_level',
        'education_level',
        'salary_min',
        'salary_max',
        'salary_type',
        'employment_type',
        'min_salary',
        'max_salary',
        'salary_currency',
        'salary_period',
        'salary_negotiable',
        'remote_work',
        'relocation_assistance',
        'application_deadline',
        'positions_available',
        'status',
        'is_featured',
        'is_premium',
        'featured_until',
        'premium_until',
        'views_count',
        'applications_count',
        'tags',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'salary_negotiable' => 'boolean',
        'remote_work' => 'boolean',
        'relocation_assistance' => 'boolean',
        'is_featured' => 'boolean',
        'is_premium' => 'boolean',
        'featured_until' => 'datetime',
        'premium_until' => 'datetime',
        'application_deadline' => 'date',
        'tags' => 'array',
    ];

    /**
     * Get the company that posted the job.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the user who posted the job.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the job category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(JobCategory::class);
    }

    /**
     * Get the job location.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(JobLocation::class, 'location_id');
    }

    /**
     * Get the job applications.
     */
    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    /**
     * Get the skills required for this job.
     */
    public function skills(): MorphToMany
    {
        return $this->morphToMany(Skill::class, 'skillable')
            ->withPivot('proficiency_level', 'years_experience')
            ->withTimestamps();
    }

    /**
     * Get the salary range as a formatted string.
     */
    public function getSalaryRangeAttribute(): string
    {
        if ($this->min_salary && $this->max_salary) {
            return $this->min_salary . ' - ' . $this->max_salary . ' ' . $this->salary_currency;
        } elseif ($this->min_salary) {
            return $this->min_salary . '+ ' . $this->salary_currency;
        } elseif ($this->max_salary) {
            return 'Up to ' . $this->max_salary . ' ' . $this->salary_currency;
        }
        
        return $this->salary_negotiable ? 'Negotiable' : 'Not specified';
    }

    /**
     * Get the employment type as a readable string.
     */
    public function getEmploymentTypeTextAttribute(): string
    {
        return match($this->employment_type) {
            'full_time' => 'Full Time',
            'part_time' => 'Part Time',
            'contract' => 'Contract',
            'internship' => 'Internship',
            'freelance' => 'Freelance',
            'temporary' => 'Temporary',
            default => 'Not specified'
        };
    }

    /**
     * Get the experience level as a readable string.
     */
    public function getExperienceLevelTextAttribute(): string
    {
        return match($this->experience_level) {
            'entry' => 'Entry Level',
            'junior' => 'Junior',
            'mid' => 'Mid Level',
            'senior' => 'Senior',
            'expert' => 'Expert',
            default => 'Not specified'
        };
    }

    /**
     * Check if the job is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if the job is featured.
     */
    public function isFeatured(): bool
    {
        return $this->is_featured && (!$this->featured_until || $this->featured_until->isFuture());
    }

    /**
     * Check if the job is premium.
     */
    public function isPremium(): bool
    {
        return $this->is_premium && (!$this->premium_until || $this->premium_until->isFuture());
    }

    /**
     * Check if the job is still accepting applications.
     */
    public function isAcceptingApplications(): bool
    {
        return $this->isActive() && 
               (!$this->application_deadline || $this->application_deadline->isFuture()) &&
               $this->positions_available > 0;
    }

    /**
     * Scope a query to only include active jobs.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include featured jobs.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)
                    ->where(function($q) {
                        $q->whereNull('featured_until')
                          ->orWhere('featured_until', '>', now());
                    });
    }

    /**
     * Scope a query to only include premium jobs.
     */
    public function scopePremium($query)
    {
        return $query->where('is_premium', true)
                    ->where(function($q) {
                        $q->whereNull('premium_until')
                          ->orWhere('premium_until', '>', now());
                    });
    }

    /**
     * Scope a query to only include jobs that accept applications.
     */
    public function scopeAcceptingApplications($query)
    {
        return $query->where('status', 'active')
                    ->where('positions_available', '>', 0)
                    ->where(function($q) {
                        $q->whereNull('application_deadline')
                          ->orWhere('application_deadline', '>', now());
                    });
    }

    /**
     * Scope a query to only include recommended jobs.
     * Recommended jobs are active jobs with high views/applications ratio or recent jobs.
     */
    public function scopeRecommended($query)
    {
        return $query->where('status', 'active')
                    ->where('positions_available', '>', 0)
                    ->where(function($q) {
                        $q->whereNull('application_deadline')
                          ->orWhere('application_deadline', '>', now());
                    })
                    ->where(function($q) {
                        // Jobs with high engagement (views/applications ratio)
                        $q->whereRaw('views_count > 0 AND applications_count > 0')
                          ->orWhere('views_count', '>', 10)
                          ->orWhere('created_at', '>', now()->subDays(7)); // Recent jobs within 7 days
                    })
                    ->orderByRaw('(views_count + applications_count) DESC')
                    ->orderBy('created_at', 'desc');
    }
}
