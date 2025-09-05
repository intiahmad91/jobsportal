<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'logo',
        'website',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'industry',
        'company_size',
        'founded_year',
        'mission',
        'vision',
        'benefits',
        'social_links',
        'is_verified',
        'is_featured',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_verified' => 'boolean',
        'is_featured' => 'boolean',
        'benefits' => 'array',
        'social_links' => 'array',
    ];

    /**
     * Get the user that owns the company.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the jobs posted by this company.
     */
    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class);
    }

    /**
     * Get the company's full address.
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->country,
            $this->postal_code
        ]);

        return implode(', ', $parts);
    }

    /**
     * Get the company's location (city, country).
     */
    public function getLocationAttribute(): string
    {
        $parts = array_filter([
            $this->city,
            $this->country
        ]);

        return implode(', ', $parts);
    }

    /**
     * Check if the company is verified.
     */
    public function isVerified(): bool
    {
        return $this->is_verified;
    }

    /**
     * Check if the company is featured.
     */
    public function isFeatured(): bool
    {
        return $this->is_featured;
    }

    /**
     * Check if the company is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Get the company's status as a readable string.
     */
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'active' => 'Active',
            'inactive' => 'Inactive',
            'suspended' => 'Suspended',
            'pending' => 'Pending',
            default => 'Unknown'
        };
    }

    /**
     * Scope a query to only include verified companies.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope a query to only include featured companies.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include active companies.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include pending companies.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
