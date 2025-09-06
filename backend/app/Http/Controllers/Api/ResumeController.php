<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Resume;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ResumeController extends Controller
{
    /**
     * Get all resumes for authenticated user
     */
    public function index()
    {
        try {
            $user = Auth::user();
            $resumes = $user->resumes()
                ->where('status', 'active')
                ->orderBy('is_default', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Resumes retrieved successfully',
                'data' => $resumes->map(function ($resume) {
                    return [
                        'id' => $resume->id,
                        'title' => $resume->title,
                        'original_name' => $resume->original_name,
                        'file_size' => $resume->file_size,
                        'file_size_formatted' => $resume->file_size_formatted,
                        'file_url' => $resume->file_url,
                        'mime_type' => $resume->mime_type,
                        'description' => $resume->description,
                        'is_default' => $resume->is_default,
                        'status' => $resume->status,
                        'uploaded_at' => $resume->created_at->format('Y-m-d H:i:s'),
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve resumes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload a new resume
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'resume' => 'required|file|max:10240', // 10MB max - no file type validation
                'title' => 'nullable|string|max:255',
                'description' => 'nullable|string|max:1000',
                'is_default' => 'nullable|string|in:true,false,1,0'
            ]);

            $user = Auth::user();
            $file = $request->file('resume');
            
            // Generate unique filename
            $filename = 'resume_' . $user->id . '_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('resumes', $filename, 'public');
            $url = Storage::url($path);

            // Convert string to boolean
            $isDefault = in_array($request->input('is_default'), ['true', '1', '1.0', 1, true], true);

            // If setting as default, unset other defaults
            if ($isDefault) {
                $user->resumes()->update(['is_default' => false]);
            }

            $resume = $user->resumes()->create([
                'title' => $request->title ?: $file->getClientOriginalName(),
                'original_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_url' => $url,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'description' => $request->description,
                'is_default' => $isDefault,
                'status' => 'active'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Resume uploaded successfully',
                'data' => [
                    'id' => $resume->id,
                    'title' => $resume->title,
                    'original_name' => $resume->original_name,
                    'file_size' => $resume->file_size,
                    'file_size_formatted' => $resume->file_size_formatted,
                    'file_url' => $resume->file_url,
                    'mime_type' => $resume->mime_type,
                    'description' => $resume->description,
                    'is_default' => $resume->is_default,
                    'status' => $resume->status,
                    'uploaded_at' => $resume->created_at->format('Y-m-d H:i:s'),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload resume',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Set a resume as default
     */
    public function setDefault($id)
    {
        try {
            $user = Auth::user();
            $resume = $user->resumes()->findOrFail($id);

            // Unset all other defaults
            $user->resumes()->update(['is_default' => false]);

            // Set this one as default
            $resume->update(['is_default' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Default resume updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to set default resume',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a resume
     */
    public function destroy($id)
    {
        try {
            $user = Auth::user();
            $resume = $user->resumes()->findOrFail($id);

            // Delete file from storage
            if (Storage::disk('public')->exists($resume->file_path)) {
                Storage::disk('public')->delete($resume->file_path);
            }

            // Delete from database
            $resume->delete();

            return response()->json([
                'success' => true,
                'message' => 'Resume deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete resume',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
