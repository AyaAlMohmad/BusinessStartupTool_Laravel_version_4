<?php
// app/Http/Controllers/API/StoryController.php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Story;
use App\Models\StoryBusinessPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoryController extends Controller
{
    public function index()
    {
        // إن أردت تقييدها على المستخدم الحالي فقط، أزل التعليق عن where('user_id', Auth::id())
        $stories = Story::with(['user', 'businessPhotos'])
            // ->where('user_id', Auth::id())
            ->get();

        return response()->json([
            'success' => true,
            'data' => $stories
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'businessName'        => 'nullable|string|max:255',
            'myStory'             => 'nullable|string',
            'businessDescription' => 'nullable|string',
            'businessSolution'    => 'nullable|string',
            'businessImpact'      => 'nullable|string',
            'futurePlans'         => 'nullable|string',
            'email'               => 'nullable|email|max:255',
            'website'             => 'nullable|url|max:255',
            'phone'               => 'nullable|string|max:50',

            'profilePhoto'        => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',

            // مصفوفة صور أو حقول متفرقة
            'businessPhotos.*'    => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        try {
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
                'user_id'              => Auth::id(),
                'business_name'        => $request->businessName,
                'my_story'             => $request->myStory,
                'business_description' => $request->businessDescription,
                'business_solution'    => $request->businessSolution,
                'business_impact'      => $request->businessImpact,
                'future_plans'         => $request->futurePlans,
                'email'                => $request->email,
                'website'              => $request->website,
                'phone'                => $request->phone,
                'profile_photo'        => $profilePhotoPath,
            ]);

            // 3) معالجة صور البيزنس (طريقتين: businessPhotos[] أو businessPhoto0/1/2...)
            $businessFiles = [];

            // A) businessPhotos[]
            if ($request->hasFile('businessPhotos')) {
                $businessFiles = array_merge($businessFiles, $request->file('businessPhotos'));
            }

            // B) businessPhoto0, businessPhoto1, ... (نجمع أي حقل يبدأ بـ businessPhoto)
            foreach ($request->files as $key => $file) {
                if (preg_match('/^businessPhoto\d+$/', $key)) {
                    $businessFiles[] = $file;
                }
            }

            foreach ($businessFiles as $file) {
                if (!$file) continue;
                $imageName = time() . '_biz_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('images'), $imageName);
                $path = '/images/' . $imageName;

                StoryBusinessPhoto::create([
                    'story_id' => $story->id,
                    'path'     => $path,
                ]);
            }

            $story->load('businessPhotos');

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
            $story = Story::with(['user', 'businessPhotos'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $story
            ]);
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

            $request->validate([
                'businessName'        => 'sometimes|nullable|string|max:255',
                'myStory'             => 'sometimes|nullable|string',
                'businessDescription' => 'sometimes|nullable|string',
                'businessSolution'    => 'sometimes|nullable|string',
                'businessImpact'      => 'sometimes|nullable|string',
                'futurePlans'         => 'sometimes|nullable|string',
                'email'               => 'sometimes|nullable|email|max:255',
                'website'             => 'sometimes|nullable|url|max:255',
                'phone'               => 'sometimes|nullable|string|max:50',

                'profilePhoto'        => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
                'businessPhotos.*'    => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            ]);

            // تحديث الحقول النصية عند تواجدها
            $map = [
                'businessName'        => 'business_name',
                'myStory'             => 'my_story',
                'businessDescription' => 'business_description',
                'businessSolution'    => 'business_solution',
                'businessImpact'      => 'business_impact',
                'futurePlans'         => 'future_plans',
                'email'               => 'email',
                'website'             => 'website',
                'phone'               => 'phone',
            ];
            foreach ($map as $in => $col) {
                if ($request->has($in)) {
                    $story->$col = $request->input($in);
                }
            }

            // تحديث صورة البروفايل (إن أُرسلت جديدة): نحذف القديمة من مجلد public
            if ($request->hasFile('profilePhoto')) {
                // حذف القديمة إن وجدت
                if ($story->profile_photo && file_exists(public_path($story->profile_photo))) {
                    @unlink(public_path($story->profile_photo));
                }
                $file = $request->file('profilePhoto');
                $imageName = time() . '_profile_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('images'), $imageName);
                $story->profile_photo = '/images/' . $imageName;
            }

            $story->save();

            // إضافة صور بيزنس جديدة (لا نحذف القديمة تلقائيًا إلا لو طلبت ذلك)
            $businessFiles = [];
            if ($request->hasFile('businessPhotos')) {
                $businessFiles = array_merge($businessFiles, $request->file('businessPhotos'));
            }
            foreach ($request->files as $key => $file) {
                if (preg_match('/^businessPhoto\d+$/', $key)) {
                    $businessFiles[] = $file;
                }
            }

            foreach ($businessFiles as $file) {
                if (!$file) continue;
                $imageName = time() . '_biz_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('images'), $imageName);
                $path = '/images/' . $imageName;

                StoryBusinessPhoto::create([
                    'story_id' => $story->id,
                    'path'     => $path,
                ]);
            }

            $story->load('businessPhotos');

            return response()->json([
                'success' => true,
                'message' => 'Story updated successfully',
                'data' => $story
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update story or story not owned by you',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $story = Story::where('user_id', Auth::id())->with('businessPhotos')->findOrFail($id);

            // حذف صورة البروفايل
            if ($story->profile_photo && file_exists(public_path($story->profile_photo))) {
                @unlink(public_path($story->profile_photo));
            }

            // حذف صور البيزنس من المجلد
            foreach ($story->businessPhotos as $photo) {
                if ($photo->path && file_exists(public_path($photo->path))) {
                    @unlink(public_path($photo->path));
                }
            }

            // سيحذف Pivot تلقائياً بسبب onDelete('cascade')، لكن نحذف السجلات يدويًا ثم القصة
            $story->businessPhotos()->delete();
            $story->delete();

            return response()->json([
                'success' => true,
                'message' => 'Story deleted successfully'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete story or story not owned by you',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
