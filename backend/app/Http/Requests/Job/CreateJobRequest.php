<?php

namespace App\Http\Requests\Job;

use Illuminate\Foundation\Http\FormRequest;

class CreateJobRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'responsibilities' => 'nullable|string',
            'benefits' => 'nullable|string',
            'category_id' => 'required|exists:job_categories,id',
            'location' => 'required|string|max:255',
            'employment_type' => 'required|in:full_time,part_time,contract,internship,freelance,temporary',
            'experience_level' => 'required|in:entry,junior,mid,senior,expert',
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

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Job title is required',
            'description.required' => 'Job description is required',
            'category_id.required' => 'Job category is required',
            'category_id.exists' => 'Selected category does not exist',
            'location.required' => 'Job location is required',
            'employment_type.required' => 'Employment type is required',
            'employment_type.in' => 'Invalid employment type selected',
            'experience_level.required' => 'Experience level is required',
            'experience_level.in' => 'Invalid experience level selected',
            'max_salary.gte' => 'Maximum salary must be greater than or equal to minimum salary',
            'application_deadline.after' => 'Application deadline must be a future date',
            'positions_available.min' => 'At least 1 position must be available',
        ];
    }
}
