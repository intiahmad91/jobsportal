<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'requirements' => $this->requirements,
            'benefits' => $this->benefits,
            'min_salary' => $this->min_salary,
            'max_salary' => $this->max_salary,
            'salary_currency' => $this->salary_currency,
            'salary_period' => $this->salary_period,
            'employment_type' => $this->employment_type,
            'experience_level' => $this->experience_level,
            'status' => $this->status,
            'is_featured' => $this->is_featured,
            'is_premium' => $this->is_premium,
            'views_count' => $this->views_count,
            'applications_count' => $this->applications_count ?? 0,
            'deadline' => $this->deadline,
            'tags' => $this->tags,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'company' => $this->whenLoaded('company', function () {
                return [
                    'id' => $this->company->id,
                    'name' => $this->company->name,
                    'logo' => $this->company->logo,
                    'website' => $this->company->website,
                ];
            }),
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                    'slug' => $this->category->slug,
                ];
            }),
            'location' => $this->whenLoaded('location', function () {
                return [
                    'id' => $this->location->id,
                    'name' => $this->location->name,
                    'city' => $this->location->city,
                    'state' => $this->location->state,
                    'country' => $this->location->country,
                    'slug' => $this->location->slug,
                ];
            }),
        ];
    }
}
