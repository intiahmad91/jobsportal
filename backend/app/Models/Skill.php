<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'category',
        'icon',
        'color',
        'is_active',
        'usage_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function users()
    {
        return $this->morphedByMany(User::class, 'skillable')
            ->withPivot('proficiency_level', 'years_experience', 'is_endorsed', 'endorsement_count')
            ->withTimestamps();
    }

    public function jobs()
    {
        return $this->morphedByMany(Job::class, 'skillable')
            ->withPivot('proficiency_level', 'years_experience')
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('usage_count', 'desc')->orderBy('name');
    }
}
