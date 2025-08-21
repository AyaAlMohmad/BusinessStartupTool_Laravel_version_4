<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\BusinessIdea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class BusinessIdeaController extends Controller
{

    public function show($id)
    {
        $businessId = $this->getValidatedBusinessId(request());

        $idea = BusinessIdea::where('id', $id)
            ->where('business_id', $businessId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return response()->json($idea);
    }


    public function index(Request $request)
    {
        $businessId = $this->getValidatedBusinessId($request);

        $latestIdea = BusinessIdea::where('user_id', Auth::id())
            ->where('business_id', $businessId)
            ->latest()
            ->first();

        return response()->json($latestIdea);
    }


    public function update(Request $request, $id)
    {
        $businessId = $this->getValidatedBusinessId($request);

        $data = $request->validate([
            'skills_experience' => 'nullable|array',
            'passions_interests' => 'nullable|array',
            'values_goals' => 'nullable|array',
            'business_ideas' => 'nullable|array',
            'personal_notes'=>'nullable|array'
        ]);

        $idea = BusinessIdea::where('id', $id)
            ->where('business_id', $businessId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $idea->update($data);
        return response()->json($idea);
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'skills_experience' => 'nullable|array',
            'passions_interests' => 'nullable|array',
            'values_goals' => 'nullable|array',
            'business_ideas' => 'nullable|array',
            'personal_notes'=>'nullable|array'
        ]);

        $businessId = $request->header('business_id');


        if (!$businessId) {
            return response()->json(['message' => 'business_id header is required'], 422);
        }

        $business = Business::where('id', $businessId)
                           ->where('user_id', Auth::id())
                           ->first();

        if (!$business) {
            return response()->json(['message' => 'Unauthorized access to business'], 403);
        }

        $data['business_id'] = $businessId;
        $data['user_id'] = Auth::id();

        $businessIdea = BusinessIdea::create($data);

        return response()->json($businessIdea, 201);
    }
    public function updateProgress(Request $request)
    {
        $businessId = $this->getValidatedBusinessId($request);

        $request->validate([
            'progress' => 'required|boolean',
        ]);

        // البحث عن سجل BusinessIdea الحالي
        $businessIdea = BusinessIdea::where('user_id', Auth::id())
            ->where('business_id', $businessId)
            ->latest()
            ->first();

    
        if (!$businessIdea) {
            $businessIdea = BusinessIdea::create([
                'user_id' => Auth::id(),
                'business_id' => $businessId,
                'progress' => $request->progress,
                'skills_experience' => [],
                'passions_interests' => [],
                'values_goals' => [],
                'business_ideas' => [],
                'personal_notes' => []
            ]);

            return response()->json([
                'message' => 'Business idea created with progress updated',
                'data' => $businessIdea
            ], 201);
        }


        $businessIdea->update(['progress' => $request->progress]);

        return response()->json([
            'message' => 'Progress updated successfully',
            'data' => $businessIdea
        ], 200);
    }

    private function getValidatedBusinessId(Request $request)
    {
        $businessId = $request->header('business_id');


        if (!$businessId) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, 'Missing business_id header');
        }


        $business = Business::where('id', $businessId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$business) {
            abort(Response::HTTP_FORBIDDEN, 'Unauthorized access to business');
        }

        return $businessId;
    }
}
