<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class ImageController extends Controller
{
    /**
     * Display a listing of the resource for the authenticated user.
     */
    public function index()
    {
        try {
            // جلب الصور الخاصة بالمستخدم الحالي
            $images = Image::with('user')
                ->where('user_id', Auth::id())
                ->latest()
                ->get();



            return response()->json([
                'success' => true,
                'data' => $images,
                'message' => 'Your images retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve images: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            if ($request->hasFile('image')) {
                if (!File::exists(public_path('images'))) {
                    File::makeDirectory(public_path('images'), 0755, true);
                }

                $file = $request->file('image');
                $imageName = time() . '_image_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('images'), $imageName);
                $imagePath = '/images/' . $imageName;

                $image = Image::create([
                    'image' => $imagePath,
                    'user_id' => Auth::id()
                ]);

                $image->load('user');
                $image->full_image_url = url($imagePath);

                return response()->json([
                    'success' => true,
                    'data' => $image,
                    'message' => 'Image uploaded successfully'
                ], 201);
            }

            return response()->json([
                'success' => false,
                'message' => 'No image file provided'
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $image = Image::with('user')
                ->where('user_id', Auth::id())
                ->find($id);

            if (!$image) {
                return response()->json([
                    'success' => false,
                    'message' => 'Image not found or not owned by you'
                ], 404);
            }

            $image->full_image_url = url($image->image);

            return response()->json([
                'success' => true,
                'data' => $image,
                'message' => 'Image retrieved successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $image = Image::where('user_id', Auth::id())->find($id);

            if (!$image) {
                return response()->json([
                    'success' => false,
                    'message' => 'Image not found or not owned by you'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            if ($request->hasFile('image')) {
                if ($image->image && File::exists(public_path($image->image))) {
                    File::delete(public_path($image->image));
                }

                $file = $request->file('image');
                $imageName = time() . '_image_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('images'), $imageName);
                $imagePath = '/images/' . $imageName;

                $image->update(['image' => $imagePath]);

                $image->load('user');
                $image->full_image_url = url($imagePath);

                return response()->json([
                    'success' => true,
                    'data' => $image,
                    'message' => 'Image updated successfully'
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'No image file provided'
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $image = Image::where('user_id', Auth::id())->find($id);

            if (!$image) {
                return response()->json([
                    'success' => false,
                    'message' => 'Image not found or not owned by you'
                ], 404);
            }

            if ($image->image && File::exists(public_path($image->image))) {
                File::delete(public_path($image->image));
            }

            $image->delete();

            return response()->json([
                'success' => true,
                'message' => 'Image deleted successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get images by specific user ID (admin/other user).
     */
    public function getUserImages($userId)
    {
        try {
            $images = Image::with('user')
                ->where('user_id', $userId)
                ->latest()
                ->get();



            return response()->json([
                'success' => true,
                'data' => $images,
                'message' => 'User images retrieved successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user images: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current authenticated user's images.
     */
    public function getMyImages()
    {
        return $this->index(); 
    }
}
