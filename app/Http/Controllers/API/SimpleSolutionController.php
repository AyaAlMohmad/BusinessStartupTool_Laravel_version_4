<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\SimpleSolution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SimpleSolutionController extends Controller
{
    public function index(Request $request)
    {
        $businessId = $this->getValidatedBusinessId($request);

        $latest = SimpleSolution::where('user_id', Auth::id())
            ->where('business_id', $businessId)
            ->latest()
            ->first();

        return response()->json($latest, 200);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'big_solution' => 'nullable|array',
            'entry_strategy' => 'nullable|array',
            'things' => 'nullable|array',
            'start_point' => 'nullable|array',
            'validation_questions' => 'nullable|array',
            'future_plan' => 'nullable|array',
            'notes' => 'nullable|array',
        ]);

        $businessId = $this->getValidatedBusinessId($request);
        $validatedData['user_id'] = Auth::id();
        $validatedData['business_id'] = $businessId;

        $solution = SimpleSolution::create($validatedData);

        return response()->json(['message' => 'Simple solution created successfully', 'data' => $solution], 201);
    }

    public function show($id, Request $request)
    {
        $businessId = $this->getValidatedBusinessId($request);

        $solution = SimpleSolution::where('id', $id)
            ->where('user_id', Auth::id())
            ->where('business_id', $businessId)
            ->firstOrFail();

        return response()->json($solution, 200);
    }

    public function update(Request $request, $id)
    {
        $businessId = $this->getValidatedBusinessId($request);

        $solution = SimpleSolution::where('id', $id)
            ->where('user_id', Auth::id())
            ->where('business_id', $businessId)
            ->firstOrFail();

        $validatedData = $request->validate([
            'big_solution' => 'nullable|array',
            'entry_strategy' => 'nullable|array',
            'things' => 'nullable|array',
            'start_point' => 'nullable|array',
            'validation_questions' => 'nullable|array',
            'future_plan' => 'nullable|array',
            'notes' => 'nullable|array',
        ]);

        $solution->update($validatedData);

        return response()->json(['message' => 'Simple solution updated successfully', 'data' => $solution], 200);
    }

    public function destroy($id, Request $request)
    {
        $businessId = $this->getValidatedBusinessId($request);

        SimpleSolution::where('id', $id)
            ->where('user_id', Auth::id())
            ->where('business_id', $businessId)
            ->delete();

        return response()->json(['message' => 'Simple solution deleted successfully'], 204);
    }

/**
     * progress 
     *
     */
    public function updateProgress(Request $request)
    {
        $request->validate([
            'progress' => 'required|boolean',
        ]);

        $businessId = $this->getValidatedBusinessId($request);

        $simpleSolution = SimpleSolution::where('user_id', Auth::id())
            ->where('business_id', $businessId)
            ->latest()
            ->first();


        if (!$simpleSolution) {
            $simpleSolution = SimpleSolution::create([
                'user_id' => Auth::id(),
                'business_id' => $businessId,
                'progress' => $request->progress,
                'big_solution' => [],
                'entry_strategy' => [],
                'things' => [],
                'start_point' => [],
                'validation_questions' => [],
                'future_plan' => [],
                'notes' => []
            ]);

            return response()->json([
                'message' => 'Simple solution record created with progress updated',
                'data' => $simpleSolution
            ], 201);
        }

        $simpleSolution->update(['progress' => $request->progress]);

        return response()->json([
            'message' => 'Progress updated successfully',
            'data' => $simpleSolution
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
