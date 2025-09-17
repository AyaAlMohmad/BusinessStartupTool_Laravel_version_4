<?php
// app/Http/Controllers/API/StoryController.php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Story;
use App\Models\StoryBusinessPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class StoryController extends Controller
{
    public function index()
    {
        try {
            $stories = Story::select([
                    'id',
                    'user_id',
                    'name',
                    'business_name',
                    'my_story',
                    'business_description',
                    'business_solution',
                    'my_vision',
                    'future_plans',
                    'email',
                    'website',
                    'phone',
                    'profile_photo',
                    'created_at',
                    'updated_at'
                ])
                ->with(['user:id,name,email', 'businessPhotos:id,story_id,path'])
                ->where('user_id', Auth::id())
                ->get();

            // إضافة روابط كاملة للصور
            $stories->transform(function ($story) {
                if ($story->profile_photo) {
                    $story->profile_photo_url = url($story->profile_photo);
                }
                if ($story->businessPhotos) {
                    $story->businessPhotos->transform(function ($photo) {
                        $photo->path_url = url($photo->path);
                        return $photo;
                    });
                }
                return $story;
            });

            return response()->json([
                'success' => true,
                'data' => $stories
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve stories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'businessName' => 'nullable|string|max:255',
            'myStory' => 'nullable|string',
            'businessDescription' => 'nullable|string',
            'businessSolution' => 'nullable|string',
            'myVision' => 'nullable|string',
            'futurePlans' => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'phone' => 'nullable|string|max:20',
            'profilePhoto' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'businessPhotos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // التأكد من وجود مجلد الصور
            if (!File::exists(public_path('images'))) {
                File::makeDirectory(public_path('images'), 0755, true);
            }

            // 1) معالجة صورة البروفايل
            $profilePhotoPath = null;
            if ($request->hasFile('profilePhoto')) {
                $file = $request->file('profilePhoto');
                $imageName = time() . '_profile_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('images'), $imageName);
                $profilePhotoPath = '/images/' . $imageName;
            }

            // 2) إنشاء الستوري
            $story = Story::create([
                'user_id' => Auth::id(),
                'name' => $request->name,
                'business_name' => $request->businessName,
                'my_story' => $request->myStory,
                'business_description' => $request->businessDescription,
                'business_solution' => $request->businessSolution,
                'my_vision' => $request->myVision,
                'future_plans' => $request->futurePlans,
                'email' => $request->email,
                'website' => $request->website,
                'phone' => $request->phone,
                'profile_photo' => $profilePhotoPath,
            ]);

            // 3) معالجة صور البيزنس
            $this->processBusinessPhotos($request, $story);

            $story->load('businessPhotos');

            // إضافة روابط كاملة للصور
            if ($story->profile_photo) {
                $story->profile_photo_url = url($story->profile_photo);
            }
            if ($story->businessPhotos) {
                $story->businessPhotos->transform(function ($photo) {
                    $photo->path_url = url($photo->path);
                    return $photo;
                });
            }

            return response()->json([
                'success' => true,
                'message' => 'Story created successfully',
                'data' => $story
            ], 201);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create story',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $story = Story::select([
                    'id',
                    'user_id',
                    'name',
                    'business_name',
                    'my_story',
                    'business_description',
                    'business_solution',
                    'my_vision',
                    'future_plans',
                    'email',
                    'website',
                    'phone',
                    'profile_photo',
                    'created_at',
                    'updated_at'
                ])
                ->with(['user:id,name,email', 'businessPhotos:id,story_id,path'])
                ->findOrFail($id);

            // إضافة روابط كاملة للصور
            if ($story->profile_photo) {
                $story->profile_photo_url = url($story->profile_photo);
            }
            if ($story->businessPhotos) {
                $story->businessPhotos->transform(function ($photo) {
                    $photo->path_url = url($photo->path);
                    return $photo;
                });
            }

            return response()->json([
                'success' => true,
                'data' => $story
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Story not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $story = Story::where('user_id', Auth::id())->with('businessPhotos')->findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'businessName' => 'sometimes|nullable|string|max:255',
                'myStory' => 'sometimes|nullable|string',
                'businessDescription' => 'sometimes|nullable|string',
                'businessSolution' => 'sometimes|nullable|string',
                'myVision' => 'sometimes|nullable|string',
                'futurePlans' => 'sometimes|nullable|string',
                'email' => 'sometimes|nullable|email|max:255',
                'website' => 'sometimes|nullable|url|max:255',
                'phone' => 'sometimes|nullable|string|max:20',
                'profilePhoto' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
                'businessPhotos.*' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            // تحديث الحقول النصية
            $updateData = [];
            $fields = [
                'name', 'business_name', 'my_story', 'business_description',
                'business_solution', 'my_vision', 'future_plans', 'email', 'website', 'phone'
            ];

            foreach ($fields as $field) {
                $requestField = $this->camelToSnake($field);
                if ($request->has($requestField)) {
                    $updateData[$field] = $request->input($requestField);
                }
            }

            // تحديث صورة البروفايل
            if ($request->hasFile('profilePhoto')) {
                // حذف الصورة القديمة
                if ($story->profile_photo && File::exists(public_path($story->profile_photo))) {
                    File::delete(public_path($story->profile_photo));
                }

                $file = $request->file('profilePhoto');
                $imageName = time() . '_profile_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('images'), $imageName);
                $updateData['profile_photo'] = '/images/' . $imageName;
            }

            $story->update($updateData);

            // معالجة صور البيزنس الجديدة
            $this->processBusinessPhotos($request, $story);

            $story->load('businessPhotos');

            // إضافة روابط كاملة للصور
            if ($story->profile_photo) {
                $story->profile_photo_url = url($story->profile_photo);
            }
            if ($story->businessPhotos) {
                $story->businessPhotos->transform(function ($photo) {
                    $photo->path_url = url($photo->path);
                    return $photo;
                });
            }

            return response()->json([
                'success' => true,
                'message' => 'Story updated successfully',
                'data' => $story
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update story',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $story = Story::where('user_id', Auth::id())->with('businessPhotos')->findOrFail($id);

            // حذف صورة البروفايل
            if ($story->profile_photo && File::exists(public_path($story->profile_photo))) {
                File::delete(public_path($story->profile_photo));
            }

            // حذف صور البيزنس
            foreach ($story->businessPhotos as $photo) {
                if ($photo->path && File::exists(public_path($photo->path))) {
                    File::delete(public_path($photo->path));
                }
            }

            // حذف السجلات من قاعدة البيانات
            $story->businessPhotos()->delete();
            $story->delete();

            return response()->json([
                'success' => true,
                'message' => 'Story deleted successfully'
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete story',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * معالجة صور البيزنس
     */
    private function processBusinessPhotos(Request $request, Story $story)
    {
        $businessFiles = [];

        // من مصفوفة businessPhotos
        if ($request->hasFile('businessPhotos')) {
            $businessFiles = array_merge($businessFiles, $request->file('businessPhotos'));
        }

        // من الحقول المنفردة businessPhoto0, businessPhoto1, etc.
        foreach ($request->allFiles() as $key => $file) {
            if (preg_match('/^businessPhoto\d+$/', $key)) {
                $businessFiles[] = $file;
            }
        }

        foreach ($businessFiles as $file) {
            if (!$file || !$file->isValid()) continue;

            $imageName = time() . '_biz_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images'), $imageName);
            $path = '/images/' . $imageName;

            StoryBusinessPhoto::create([
                'story_id' => $story->id,
                'path' => $path,
            ]);
        }
    }

    /**
     * تحويل camelCase إلى snake_case
     */
    private function camelToSnake($string)
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $string));
    }
}
