<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\SettingsController;
use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\JobSeekerController;
use App\Http\Controllers\Api\ResumeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

// Test route to check if API is working
Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is working!'
    ]);
});

// Admin dashboard route (temporarily without auth for testing)
Route::get('/admin/dashboard', [App\Http\Controllers\Api\AdminController::class, 'dashboard']);


// Simple echo route to test request handling
Route::post('/echo', function (Illuminate\Http\Request $request) {
    return response()->json([
        'method' => $request->method(),
        'content_type' => $request->header('Content-Type'),
        'raw_content' => $request->getContent(),
        'all_data' => $request->all(),
        'json_data' => $request->json()->all(),
        'input_data' => $request->input()
    ]);
});

// Simple working login route
Route::post('/simple-login', function (Illuminate\Http\Request $request) {
    try {
        // Get the raw JSON content
        $rawContent = $request->getContent();
        
        // Try to decode JSON
        $data = json_decode($rawContent, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid JSON: ' . json_last_error_msg(),
                'raw_content' => $rawContent
            ], 400);
        }
        
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email and password are required'
            ], 400);
        }
        
        // Attempt authentication
        if (Illuminate\Support\Facades\Auth::attempt(['email' => $email, 'password' => $password])) {
            $user = Illuminate\Support\Facades\Auth::user();
            $token = $user->createToken('simple-token')->plainTextToken;
            
            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'user_type' => $user->profile->user_type ?? 'user'
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer',
                ]
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials'
        ], 401);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Server error: ' . $e->getMessage()
        ], 500);
    }
});

// Public job routes
Route::prefix('jobs')->group(function () {
    Route::get('/', [JobController::class, 'index']);
    Route::get('/featured', [JobController::class, 'featured']);
    Route::get('/premium', [JobController::class, 'premium']);
    Route::get('/recommended', [JobController::class, 'recommended']);
    Route::get('/stats', [JobController::class, 'stats']);
    Route::get('/{job}', [JobController::class, 'show']);
    Route::get('/company/{companyId}', [JobController::class, 'byCompany']);
});

// Public company routes
Route::prefix('companies')->group(function () {
    Route::get('/', [CompanyController::class, 'index']);
    Route::get('/{company}', [CompanyController::class, 'show']);
});

// Public job seeker routes
Route::prefix('job-seekers')->group(function () {
    Route::get('/', [JobSeekerController::class, 'index']);
    Route::get('/featured', [JobSeekerController::class, 'featured']);
    Route::get('/{id}', [JobSeekerController::class, 'show']);
});

// Public categories routes
Route::get('/categories', function () {
    return response()->json([
        'success' => true,
        'data' => \App\Models\JobCategory::active()->ordered()->get()
    ]);
});

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::get('/user', [AuthController::class, 'user']);
        Route::get('/permissions', [AuthController::class, 'permissions']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
    });

    // User routes
    Route::prefix('user')->group(function () {
        Route::get('/profile', [UserController::class, 'getProfile']);
        Route::put('/profile', [UserController::class, 'updateProfile']);
        Route::post('/avatar', [UserController::class, 'uploadAvatar']);
        Route::post('/cv', [UserController::class, 'uploadCV']);
        Route::get('/cv', [UserController::class, 'getCV']);
        Route::delete('/cv', [UserController::class, 'deleteCV']);
        Route::get('/dashboard/jobseeker', [UserController::class, 'jobseekerDashboard']);
        Route::get('/dashboard/employer', [UserController::class, 'employerDashboard']);
        Route::get('/dashboard', [UserController::class, 'dashboard']);
        Route::get('/applications', [UserController::class, 'applications']);
        Route::get('/saved-jobs', [UserController::class, 'savedJobs']);
        Route::post('/save-job/{job}', [UserController::class, 'saveJob']);
        Route::delete('/save-job/{job}', [UserController::class, 'unsaveJob']);
    });

    // Job management routes (employers only)
    Route::prefix('jobs')->group(function () {
        Route::post('/', [JobController::class, 'store']);
        Route::put('/{job}', [JobController::class, 'update']);
        Route::delete('/{job}', [JobController::class, 'destroy']);
        Route::post('/{job}/featured', [JobController::class, 'toggleFeatured']);
        Route::post('/{job}/premium', [JobController::class, 'togglePremium']);
        Route::post('/{job}/close', [JobController::class, 'close']);
    });

    // Company management routes (employers only)
    Route::prefix('my-company')->group(function () {
        Route::get('/', [CompanyController::class, 'show']);
        Route::post('/', [CompanyController::class, 'store']);
        Route::put('/', [CompanyController::class, 'update']);
        Route::post('/logo', [CompanyController::class, 'uploadLogo']);
        Route::post('/verify', [CompanyController::class, 'verify']);
    });

    // Analytics routes (employers only)
    Route::prefix('analytics')->group(function () {
        Route::get('/employer', [AnalyticsController::class, 'getEmployerAnalytics']);
    });

    // Settings routes (employers only)
    Route::prefix('settings')->group(function () {
        Route::get('/employer', [SettingsController::class, 'getEmployerSettings']);
        Route::put('/employer', [SettingsController::class, 'updateEmployerSettings']);
    });

    // Job application routes
    Route::prefix('applications')->group(function () {
        Route::post('/{job}', [ApplicationController::class, 'store']);
        Route::get('/', [ApplicationController::class, 'index']);
        Route::get('/{application}', [ApplicationController::class, 'show']);
        Route::put('/{application}', [ApplicationController::class, 'update']);
        Route::delete('/{application}', [ApplicationController::class, 'destroy']);
        Route::post('/{application}/status', [ApplicationController::class, 'updateStatus']);
        Route::post('/{application}/notes', [ApplicationController::class, 'addNotes']);
        Route::post('/{application}/favorite', [ApplicationController::class, 'toggleFavorite']);
        Route::post('/{application}/rating', [ApplicationController::class, 'addRating']);
    });

    // Resume routes
    Route::prefix('resumes')->group(function () {
        Route::get('/', [ResumeController::class, 'index']);
        Route::post('/', [ResumeController::class, 'store']);
        Route::post('/{id}/set-default', [ResumeController::class, 'setDefault']);
        Route::delete('/{id}', [ResumeController::class, 'destroy']);
    });

    // Admin routes
    Route::prefix('admin')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::get('/users/{user}', [UserController::class, 'show']);
        Route::put('/users/{user}', [UserController::class, 'update']);
        Route::delete('/users/{user}', [UserController::class, 'destroy']);
        Route::post('/users/{user}/verify', [UserController::class, 'verify']);
        Route::post('/users/{user}/suspend', [UserController::class, 'suspend']);
        
        Route::get('/jobs', [JobController::class, 'adminIndex']);
        Route::post('/jobs', [JobController::class, 'adminStore']);
        Route::get('/jobs/stats', [JobController::class, 'adminStats']);
        Route::put('/jobs/{job}', [JobController::class, 'adminUpdate']);
        Route::put('/jobs/{job}/moderate', [JobController::class, 'moderate']);
        Route::delete('/jobs/{job}', [JobController::class, 'adminDestroy']);
        
        Route::get('/companies', [CompanyController::class, 'adminIndex']);
        Route::post('/companies', [CompanyController::class, 'adminStore']);
        Route::get('/companies/stats', [CompanyController::class, 'adminStats']);
        Route::put('/companies/{company}', [CompanyController::class, 'adminUpdate']);
        Route::delete('/companies/{company}', [CompanyController::class, 'adminDestroy']);
        Route::put('/companies/{company}/verify', [CompanyController::class, 'adminVerify']);
        
        Route::get('/statistics', [UserController::class, 'statistics']);
        Route::get('/analytics', [UserController::class, 'analytics']);
        Route::get('/export-data', [UserController::class, 'exportData']);
    });
});

// Fallback route
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'API endpoint not found',
    ], 404);
});
