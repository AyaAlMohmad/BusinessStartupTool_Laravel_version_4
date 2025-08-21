<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\TestingYourIdeaResource;
use App\Models\Business;
use App\Models\TestingYourIdea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class TestingYourIdeaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $businessId = $this->getValidatedBusinessId($request);

        $ideas = TestingYourIdea::where('user_id', Auth::id())
            ->where('business_id', $businessId)
            ->latest()
            ->first();

        return new TestingYourIdeaResource($ideas) ;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'your_idea' => 'nullable|array',
            'desirability' => 'nullable|array',
            'desirability.solves_problem' => 'nullable|boolean',
            'desirability.problem_statement' => 'nullable|array',
            'desirability.existing_solutions_used' => 'nullable|boolean',
            'desirability.current_solutions_details' => 'nullable|array',
            'desirability.switch_reason' => 'nullable|array',
            'desirability.notes' => 'nullable|array',

            'feasibility' => 'nullable|array',
            'feasibility.required_skills' => 'nullable|array',
            'feasibility.qualifications_permits' => 'nullable|array',
            'feasibility.notes' => 'nullable|array',

            'viability' => 'nullable|array',
            'viability.payment_possible' => 'nullable|array',
            'viability.profitability' => 'nullable|array',
            'viability.finances_details' => 'nullable|array',
            'viability.notes' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $businessId = $this->getValidatedBusinessId($request);
        $data = $this->prepareData($validator->validated());

        $idea = TestingYourIdea::create(array_merge($data, [
            'user_id' => Auth::id(),
            'business_id' => $businessId
        ]));

        return new TestingYourIdeaResource($idea);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $businessId = $this->getValidatedBusinessId(request());

        return new TestingYourIdeaResource(TestingYourIdea::where('id', $id)
            ->where('business_id', $businessId)
            ->where('user_id', Auth::id())
            ->firstOrFail());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {


        $businessId = $this->getValidatedBusinessId(request());

        $testingYourIdea = TestingYourIdea::where('id', $id)
            ->where('business_id', $businessId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'your_idea' => 'nullable|array',
            'desirability' => 'nullable|array',
            'desirability.solves_problem' => 'nullable|boolean',
            'desirability.problem_statement' => 'nullable|array',
            'desirability.existing_solutions_used' => 'nullable|boolean',
            'desirability.current_solutions_details' => 'nullable|array',
            'desirability.switch_reason' => 'nullable|array',
            'desirability.notes' => 'nullable|array',


            'feasibility' => 'nullable|array',
            'feasibility.required_skills' => 'nullable|array',
            'feasibility.qualifications_permits' => 'nullable|array',
            'feasibility.notes' => 'nullable|array',

            'viability' => 'nullable|array',
            'viability.payment_possible' => 'nullable|array',
            'viability.profitability' => 'nullable|array',
            'viability.finances_details' => 'nullable|array',
            'viability.notes' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $this->prepareData($validator->validated());
        $testingYourIdea->update($data);

        return new TestingYourIdeaResource($testingYourIdea);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {

        $businessId = $this->getValidatedBusinessId(request());

        $testingYourIdean = TestingYourIdea::where('id', $id)
            ->where('business_id', $businessId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $testingYourIdean->delete();
        return response()->json(null, 204);
    }



    public function updateProgress(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'progress' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $businessId = $this->getValidatedBusinessId($request);


        $testingYourIdea = TestingYourIdea::where('user_id', Auth::id())
            ->where('business_id', $businessId)
            ->latest()
            ->first();


        if (!$testingYourIdea) {
            $testingYourIdea = TestingYourIdea::create([
                'user_id' => Auth::id(),
                'business_id' => $businessId,
                'progress' => $request->progress,
                'your_idea' => [],
                'solves_problem' => null,
                'problem_statement' => [],
                'existing_solutions_used' => null,
                'current_solutions_details' => [],
                'switch_reason' => [],
                'desirability_notes' => [],
                'required_skills' => [],
                'qualifications_permits' => [],
                'feasibility_notes' => [],
                'payment_possible' => [],
                'profitability' => [],
                'finances_details' => [],
                'viability_notes' => []
            ]);

            return response()->json([
                'message' => 'Testing your idea record created with progress updated',
                'data' => new TestingYourIdeaResource($testingYourIdea)
            ], 201);
        }

        $testingYourIdea->update(['progress' => $request->progress]);

        return response()->json([
            'message' => 'Progress updated successfully',
            'data' => new TestingYourIdeaResource($testingYourIdea)
        ], 200);
    }
    private function prepareData(array $data): array
    {
        return [
            // Your Idea
            'your_idea' => $data['your_idea'] ?? null,

            // Desirability
            'solves_problem' => $data['desirability']['solves_problem'] ?? null,
            'problem_statement' => $data['desirability']['problem_statement'] ?? null,
            'existing_solutions_used' => $data['desirability']['existing_solutions_used'] ?? null,
            'current_solutions_details' => $data['desirability']['current_solutions_details'] ?? null,
            'switch_reason' => $data['desirability']['switch_reason'] ?? null,
            'desirability_notes' => $data['desirability']['notes'] ?? null,

            // Feasibility
            'required_skills' => $data['feasibility']['required_skills'] ?? null,
            'qualifications_permits' => $data['feasibility']['qualifications_permits'] ?? null,
            'feasibility_notes' => $data['feasibility']['notes'] ?? null,

            // Viability
            'payment_possible' => $data['viability']['payment_possible'] ?? null,
            'profitability' => $data['viability']['profitability'] ?? null,
            'finances_details' => $data['viability']['finances_details'] ?? null,
            'viability_notes' => $data['viability']['notes'] ?? null,
        ];
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
