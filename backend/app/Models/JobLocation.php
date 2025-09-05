<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'city',
        'state',
        'country',
        'slug',
        'latitude',
        'longitude',
        'is_active',
        'job_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class, 'location_id');
    }

    public function getFullLocationAttribute(): string
    {
        $parts = array_filter([$this->city, $this->state, $this->country]);
        return implode(', ', $parts);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('country')->orderBy('state')->orderBy('city');
    }
}
