<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certification extends Model
{
    use HasFactory;

    protected $table = 'certifications';

    protected $fillable = [
        'user_id',
        'name',
        'issuing_organization',
        'credential_id',
        'credential_url',
        'issue_date',
        'expiry_date',
        'never_expires',
        'description',
        'image',
        'skills_covered',
    ];

    protected $casts = [
        'never_expires' => 'boolean',
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'skills_covered' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        if ($this->never_expires) {
            return false;
        }
        
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        if ($this->never_expires || !$this->expiry_date) {
            return false;
        }
        
        return $this->expiry_date->diffInDays(now()) <= $days;
    }

    public function getStatusAttribute(): string
    {
        if ($this->never_expires) {
            return 'Never Expires';
        }
        
        if ($this->isExpired()) {
            return 'Expired';
        }
        
        if ($this->isExpiringSoon()) {
            return 'Expiring Soon';
        }
        
        return 'Valid';
    }

    public function scopeValid($query)
    {
        return $query->where(function($q) {
            $q->where('never_expires', true)
              ->orWhere('expiry_date', '>', now());
        });
    }

    public function scopeExpired($query)
    {
        return $query->where('never_expires', false)
                    ->where('expiry_date', '<=', now());
    }

    public function scopeExpiringSoon($query, int $days = 30)
    {
        return $query->where('never_expires', false)
                    ->where('expiry_date', '>', now())
                    ->where('expiry_date', '<=', now()->addDays($days));
    }
}
