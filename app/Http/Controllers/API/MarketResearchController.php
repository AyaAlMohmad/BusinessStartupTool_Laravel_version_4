<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\MarketResearch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
class MarketResearchController extends Controller
{
    /**
     * Display a listing of the resource.
     */

        public function index(Request $request)
        {
            $businessId = $this->getValidatedBusinessId($request);

            $latestResearch = MarketResearch::where('user_id', Auth::id())
                ->where('business_id', $businessId)
                ->latest()
                ->first();

            return response()->json($latestResearch, 200);
        }








    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'target_customer_name' => 'nullable|string',
            'age' => 'nullable|string',
            'gender' => 'nullable|string',
            'other' => 'nullable|string',
            'employment' => 'nullable|string',
            'income' => 'nullable|string',
            'education' => 'nullable|string',
            'must_have_solutions' => 'nullable|array',
            'should_have_solutions' => 'nullable|array',
            'nice_to_have_solutions' => 'nullable|array',
            'nots'=> 'nullable|array',
            'solution'=> 'nullable|array',
            'problem'=> 'nullable|array',
            'help_persona'=> 'nullable|array',
        ]);

        $businessId = $this->getValidatedBusinessId($request);
        $validatedData['user_id'] = Auth::id();
        $validatedData['business_id'] = $businessId;

        $marketResearch = MarketResearch::create($validatedData);

        return response()->json(['message' => 'Market research created successfully', 'data' => $marketResearch], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $businessId = $this->getValidatedBusinessId(request());

        $marketResearch = MarketResearch::where('id', $id)
            ->where('business_id', $businessId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return response()->json($marketResearch, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $businessId = $this->getValidatedBusinessId($request);

        $marketResearch = MarketResearch::where('id', $id)
            ->where('business_id', $businessId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $validatedData = $request->validate([
            'target_customer_name' => 'nullable|string',
            'age' => 'nullable|string',
            'gender' => 'nullable|string',
            'other' => 'nullable|string',
            'employment' => 'nullable|string',
            'income' => 'nullable|string',
            'education' => 'nullable|string',
            'must_have_solutions' => 'nullable|array',
            'should_have_solutions' => 'nullable|array',
            'nice_to_have_solutions' => 'nullable|array',
            'nots'=> 'nullable|array',
            'solution'=> 'nullable|array',
            'problem'=> 'nullable|array',
            'help_persona'=> 'nullable|array',
        ]);

        $marketResearch->update($validatedData);

        return response()->json(['message' => 'Market research updated successfully', 'data' => $marketResearch], 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
        {
            $businessId = $this->getValidatedBusinessId(request());

            MarketResearch::where('id', $id)
                ->where('business_id', $businessId)
                ->where('user_id', Auth::id())
                ->delete();

            return response()->json(['message' => 'Market research deleted successfully'], 204);
        }


        public function updateProgress(Request $request)
        {
            $request->validate([
                'progress' => 'required|boolean',
            ]);

            $businessId = $this->getValidatedBusinessId($request);

            $marketResearch = MarketResearch::where('user_id', Auth::id())
                ->where('business_id', $businessId)
                ->latest()
                ->first();


            if (!$marketResearch) {
                $marketResearch = MarketResearch::create([
                    'user_id' => Auth::id(),
                    'business_id' => $businessId,
                    'progress' => $request->progress,
                    'target_customer_name' => null,
                    'age' => null,
                    'gender' => null,
                    'other' => null,
                    'employment' => null,
                    'income' => null,
                    'education' => null,
                    'must_have_solutions' => [],
                    'should_have_solutions' => [],
                    'nice_to_have_solutions' => [],
                    'nots' => [],
                    'solution' => [],
                    'problem' => [],
                    'help_persona' => []
                ]);

                return response()->json([
                    'message' => 'Market research record created with progress updated',
                    'data' => $marketResearch
                ], 201);
            }

            $marketResearch->update(['progress' => $request->progress]);

            return response()->json([
                'message' => 'Progress updated successfully',
                'data' => $marketResearch
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
