<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CompanyController extends Controller
{
    /**
     * Display a listing of companies for admin.
     */
    public function adminIndex(Request $request): JsonResponse
    {
        try {
            $query = Company::with(['user']);
            
            // Search functionality
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('industry', 'like', "%{$search}%");
                });
            }

            // Filter by status
            if ($request->has('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            // Filter by industry
            if ($request->has('industry') && $request->industry !== 'all') {
                $query->where('industry', $request->industry);
            }

            // Filter by company size
            if ($request->has('size') && $request->size !== 'all') {
                $query->where('company_size', $request->size);
            }

            $limit = $request->get('limit', 15);
            $companies = $query->latest()->paginate($limit);

            return response()->json([
                'success' => true,
                'data' => [
                    'companies' => $companies->items(),
                    'pagination' => [
                        'current_page' => $companies->currentPage(),
                        'last_page' => $companies->lastPage(),
                        'per_page' => $companies->perPage(),
                        'total' => $companies->total(),
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created company.
     */
    public function adminStore(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'website' => 'nullable|url',
                'email' => 'nullable|email',
                'phone' => 'nullable|string',
                'address' => 'nullable|string',
                'city' => 'nullable|string',
                'state' => 'nullable|string',
                'country' => 'nullable|string',
                'postal_code' => 'nullable|string',
                'industry' => 'nullable|string',
                'company_size' => 'nullable|string',
                'founded_year' => 'nullable|integer|min:1800|max:' . date('Y'),
                'mission' => 'nullable|string',
                'vision' => 'nullable|string',
                'benefits' => 'nullable|string',
                'social_links' => 'nullable|string',
                'status' => 'required|in:active,inactive,pending',
                'user_id' => 'required|exists:users,id'
            ]);

            $company = Company::create($validated);
            $company->load(['user']);

            return response()->json([
                'success' => true,
                'message' => 'Company created successfully',
                'data' => $company
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified company.
     */
    public function adminUpdate(Request $request, Company $company): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'website' => 'nullable|url',
                'email' => 'nullable|email',
                'phone' => 'nullable|string',
                'address' => 'nullable|string',
                'city' => 'nullable|string',
                'state' => 'nullable|string',
                'country' => 'nullable|string',
                'postal_code' => 'nullable|string',
                'industry' => 'nullable|string',
                'company_size' => 'nullable|string',
                'founded_year' => 'nullable|integer|min:1800|max:' . date('Y'),
                'mission' => 'nullable|string',
                'vision' => 'nullable|string',
                'benefits' => 'nullable|string',
                'social_links' => 'nullable|string',
                'status' => 'required|in:active,inactive,pending'
            ]);

            $company->update($validated);
            $company->load(['user']);

            return response()->json([
                'success' => true,
                'message' => 'Company updated successfully',
                'data' => $company
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified company.
     */
    public function adminDestroy(Company $company): JsonResponse
    {
        try {
            $company->delete();

            return response()->json([
                'success' => true,
                'message' => 'Company deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get companies statistics for admin.
     */
    public function adminStats(): JsonResponse
    {
        try {
            $stats = [
                'total' => Company::count(),
                'verified' => Company::where('is_verified', true)->count(),
                'pending' => Company::where('status', 'pending')->count(),
                'featured' => Company::where('is_featured', true)->count(),
                'companies_by_industry' => Company::selectRaw('industry, COUNT(*) as count')
                    ->whereNotNull('industry')
                    ->groupBy('industry')
                    ->pluck('count', 'industry')
                    ->toArray(),
                'companies_by_size' => Company::selectRaw('company_size, COUNT(*) as count')
                    ->whereNotNull('company_size')
                    ->groupBy('company_size')
                    ->pluck('count', 'company_size')
                    ->toArray(),
                'recent_companies' => Company::with(['user'])
                    ->latest()
                    ->limit(5)
                    ->get()
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verify a company.
     */
    public function adminVerify(Request $request, Company $company): JsonResponse
    {
        try {
            $validated = $request->validate([
                'is_verified' => 'required|boolean'
            ]);

            $company->update(['is_verified' => $validated['is_verified']]);

            return response()->json([
                'success' => true,
                'message' => 'Company verification status updated successfully',
                'data' => $company
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
