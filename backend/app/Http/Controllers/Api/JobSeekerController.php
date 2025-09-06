<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class JobSeekerController extends Controller
{
    /**
     * Get all job seekers with their profiles and skills.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = User::with(['profile', 'skills'])
                ->whereHas('profile', function ($q) {
                    $q->where('user_type', 'jobseeker')
                      ->where(function($subQ) {
                          $subQ->where('status', 'active')
                               ->orWhereNull('status');
                      });
                });

            // Apply filters
            if ($request->has('experience_levels') && !empty($request->experience_levels)) {
                $experienceLevels = is_array($request->experience_levels) ? $request->experience_levels : explode(',', $request->experience_levels);
                $query->whereHas('profile', function ($q) use ($experienceLevels) {
                    $q->whereIn('experience_level', $experienceLevels);
                });
            }

            if ($request->has('locations') && !empty($request->locations)) {
                $locations = is_array($request->locations) ? $request->locations : explode(',', $request->locations);
                $query->whereHas('profile', function ($q) use ($locations) {
                    $q->where(function($subQ) use ($locations) {
                        foreach ($locations as $location) {
                            $subQ->orWhere('location', 'like', '%' . $location . '%');
                        }
                    });
                });
            }

            if ($request->has('open_to_work') && $request->boolean('open_to_work') !== false) {
                $query->whereHas('profile', function ($q) use ($request) {
                    $q->where('open_to_work', $request->boolean('open_to_work'));
                });
            }

            if ($request->has('skills')) {
                $skills = is_array($request->skills) ? $request->skills : explode(',', $request->skills);
                $query->whereHas('skills', function ($q) use ($skills) {
                    $q->whereIn('name', $skills);
                });
            }

            // Pagination
            $perPage = $request->get('per_page', 12);
            $jobSeekers = $query->paginate($perPage);

            // Transform the data
            $transformedData = $jobSeekers->getCollection()->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->profile->full_name ?? $user->name,
                    'email' => $user->email,
                    'avatar' => $user->profile->avatar,
                    'bio' => $user->profile->bio,
                    'location' => $user->profile->location,
                    'experience_level' => $user->profile->experience_level,
                    'experience_level_text' => $user->profile->experience_level_text,
                    'employment_status' => $user->profile->employment_status,
                    'employment_status_text' => $user->profile->employment_status_text,
                    'open_to_work' => $user->profile->open_to_work,
                    'open_to_relocation' => $user->profile->open_to_relocation,
                    'open_to_remote' => $user->profile->open_to_remote,
                    'current_salary' => $user->profile->current_salary,
                    'expected_salary' => $user->profile->expected_salary,
                    'website' => $user->profile->website,
                    'linkedin' => $user->profile->linkedin,
                    'github' => $user->profile->github,
                    'portfolio' => $user->profile->portfolio,
                    'skills' => $user->skills->pluck('name')->toArray(),
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Job seekers retrieved successfully',
                'data' => $transformedData,
                'pagination' => [
                    'current_page' => $jobSeekers->currentPage(),
                    'last_page' => $jobSeekers->lastPage(),
                    'per_page' => $jobSeekers->perPage(),
                    'total' => $jobSeekers->total(),
                    'from' => $jobSeekers->firstItem(),
                    'to' => $jobSeekers->lastItem(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving job seekers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific job seeker by ID.
     */
    public function show($id): JsonResponse
    {
        try {
            $user = User::with(['profile', 'skills', 'experiences', 'educations', 'certifications'])
                ->whereHas('profile', function ($q) {
                    $q->where('user_type', 'jobseeker');
                })
                ->findOrFail($id);

            $jobSeeker = [
                'id' => $user->id,
                'name' => $user->profile->full_name ?? $user->name,
                'email' => $user->email,
                'avatar' => $user->profile->avatar,
                'bio' => $user->profile->bio,
                'phone' => $user->profile->phone,
                'location' => $user->profile->location,
                'experience_level' => $user->profile->experience_level,
                'experience_level_text' => $user->profile->experience_level_text,
                'employment_status' => $user->profile->employment_status,
                'employment_status_text' => $user->profile->employment_status_text,
                'open_to_work' => $user->profile->open_to_work,
                'open_to_relocation' => $user->profile->open_to_relocation,
                'open_to_remote' => $user->profile->open_to_remote,
                'current_salary' => $user->profile->current_salary,
                'expected_salary' => $user->profile->expected_salary,
                'website' => $user->profile->website,
                'linkedin' => $user->profile->linkedin,
                'github' => $user->profile->github,
                'portfolio' => $user->profile->portfolio,
                'preferred_job_types' => $user->profile->preferred_job_types,
                'preferred_locations' => $user->profile->preferred_locations,
                'preferred_industries' => $user->profile->preferred_industries,
                'skills' => $user->skills->map(function ($skill) {
                    return [
                        'id' => $skill->id,
                        'name' => $skill->name,
                        'proficiency_level' => $skill->pivot->proficiency_level,
                        'years_experience' => $skill->pivot->years_experience,
                        'is_endorsed' => $skill->pivot->is_endorsed,
                        'endorsement_count' => $skill->pivot->endorsement_count,
                    ];
                }),
                'experiences' => $user->experiences->map(function ($exp) {
                    return [
                        'id' => $exp->id,
                        'title' => $exp->title,
                        'company' => $exp->company,
                        'location' => $exp->location,
                        'start_date' => $exp->start_date,
                        'end_date' => $exp->end_date,
                        'is_current' => $exp->is_current,
                        'description' => $exp->description,
                    ];
                }),
                'educations' => $user->educations->map(function ($edu) {
                    return [
                        'id' => $edu->id,
                        'degree' => $edu->degree,
                        'field_of_study' => $edu->field_of_study,
                        'institution' => $edu->institution,
                        'location' => $edu->location,
                        'start_date' => $edu->start_date,
                        'end_date' => $edu->end_date,
                        'gpa' => $edu->grade,
                        'description' => $edu->description,
                    ];
                }),
                'certifications' => $user->certifications->map(function ($cert) {
                    return [
                        'id' => $cert->id,
                        'name' => $cert->name,
                        'issuing_organization' => $cert->issuing_organization,
                        'issue_date' => $cert->issue_date,
                        'expiry_date' => $cert->expiry_date,
                        'credential_id' => $cert->credential_id,
                        'credential_url' => $cert->credential_url,
                    ];
                }),
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ];

            return response()->json([
                'success' => true,
                'message' => 'Job seeker retrieved successfully',
                'data' => $jobSeeker
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving job seeker',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get featured job seekers (open to work).
     */
    public function featured(Request $request): JsonResponse
    {
        try {
            $limit = $request->get('limit', 6);
            
            $jobSeekers = User::with(['profile', 'skills'])
                ->whereHas('profile', function ($q) {
                    $q->where('user_type', 'jobseeker')
                      ->where(function($subQ) {
                          $subQ->where('status', 'active')
                               ->orWhereNull('status');
                      })
                      ->where('open_to_work', true);
                })
                ->limit($limit)
                ->get();

            $transformedData = $jobSeekers->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->profile->full_name ?? $user->name,
                    'avatar' => $user->profile->avatar,
                    'bio' => $user->profile->bio,
                    'location' => $user->profile->location,
                    'experience_level' => $user->profile->experience_level,
                    'experience_level_text' => $user->profile->experience_level_text,
                    'employment_status' => $user->profile->employment_status,
                    'open_to_work' => $user->profile->open_to_work,
                    'skills' => $user->skills->pluck('name')->toArray(),
                    'created_at' => $user->created_at,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Featured job seekers retrieved successfully',
                'data' => $transformedData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving featured job seekers',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
