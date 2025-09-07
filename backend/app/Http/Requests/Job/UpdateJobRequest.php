<?php

namespace App\Http\Requests\Job;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJobRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'requirements' => 'nullable|string',
            'responsibilities' => 'nullable|string',
            'benefits' => 'nullable|string',
            'category_id' => 'sometimes|exists:job_categories,id',
            'location' => 'sometimes|string|max:255',
            'employment_type' => 'sometimes|in:full_time,part_time,contract,internship,freelance,temporary',
            'experience_level' => 'sometimes|in:entry,junior,mid,senior,expert',
            'education_level' => 'nullable|in:high_school,bachelors,masters,phd',
            'min_salary' => 'nullable|numeric|min:0',
            'max_salary' => 'nullable|numeric|min:0|gte:min_salary',
            'salary_currency' => 'nullable|string|max:3',
            'salary_period' => 'nullable|in:hourly,daily,monthly,yearly',
            'salary_negotiable' => 'nullable|boolean',
            'remote_work' => 'nullable|boolean',
            'relocation_assistance' => 'nullable|boolean',
            'application_deadline' => 'nullable|date|after:today',
            'positions_available' => 'nullable|integer|min:1',
            'status' => 'nullable|in:active,draft,paused,closed',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
        ];
    }
}
