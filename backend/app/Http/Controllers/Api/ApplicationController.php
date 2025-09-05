<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JobApplication;

class ApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = JobApplication::with(['user', 'job.company']);
            
            $limit = $request->get('limit', 10);
            
            $applications = $query->latest()->take($limit)->get()->map(function($application) {
                return [
                    'id' => $application->id,
                    'user_name' => $application->user ? $application->user->name : 'N/A',
                    'job_title' => $application->job ? $application->job->title : 'N/A',
                    'company_name' => $application->job && $application->job->company ? $application->job->company->name : 'N/A',
                    'status' => ucfirst($application->status ?? 'pending'),
                    'created_at' => $application->created_at->format('Y-m-d H:i:s'),
                    'cover_letter' => $application->cover_letter ?? 'No cover letter provided'
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'applications' => $applications
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch applications: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $jobId)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'cover_letter' => 'nullable|string|max:2000',
                'resume_url' => 'nullable|string|max:500',
                'expected_salary' => 'nullable|numeric|min:0',
                'availability_date' => 'nullable|date|after:today',
                'notes' => 'nullable|string|max:1000'
            ]);

            // Get the authenticated user
            $user = $request->user();
            
            // Check if job exists
            $job = \App\Models\Job::findOrFail($jobId);
            
            // Check if user has already applied for this job
            $existingApplication = JobApplication::where('user_id', $user->id)
                ->where('job_id', $jobId)
                ->first();
                
            if ($existingApplication) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already applied for this job'
                ], 400);
            }

            // Create the application
            $application = JobApplication::create([
                'user_id' => $user->id,
                'job_id' => $jobId,
                'cover_letter' => $validated['cover_letter'] ?? null,
                'resume_url' => $validated['resume_url'] ?? null,
                'expected_salary' => $validated['expected_salary'] ?? null,
                'availability_date' => $validated['availability_date'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'status' => 'pending',
                'applied_at' => now()
            ]);

            // Increment application count for the job
            $job->increment('applications_count');

            return response()->json([
                'success' => true,
                'message' => 'Application submitted successfully',
                'data' => [
                    'application_id' => $application->id,
                    'job_title' => $job->title,
                    'company_name' => $job->company ? $job->company->name : 'N/A',
                    'status' => $application->status,
                    'applied_at' => $application->applied_at->format('Y-m-d H:i:s')
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit application: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
