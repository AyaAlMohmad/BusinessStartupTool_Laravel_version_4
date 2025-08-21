<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\LegalStructure;
use App\Models\LegalRequirementTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
class LegalStructureController extends Controller
{
    public function index(Request $request)
    {
        $businessId = $this->getValidatedBusinessId(request());


        $data = LegalStructure::with('tasks')

            ->where('business_id', $businessId)
            ->where('user_id', Auth::id())
            ->latest()
            ->first();

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'nullable|string|max:255',
            'notes' => 'nullable|array',
            'tasks' => 'nullable|array',
            'tasks.*.title' => 'nullable|string|max:255',
            'tasks.*.status' => 'nullable|string|max:50',
            'tasks.*.deadline' => 'nullable|date',
        ]);

        $data['user_id'] = auth()->id();
        $data['business_id'] = $this->getValidatedBusinessId(request());


        DB::beginTransaction();
        try {
            $legalStructure = LegalStructure::create($data);

            if (!empty($data['tasks'])) {
                foreach ($data['tasks'] as $taskData) {
                    $taskData['legal_structure_id'] = $legalStructure->id;
                    LegalRequirementTask::create($taskData);
                }
            }

            DB::commit();
            return response()->json($legalStructure->load('tasks'), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create record', 'details' => $e->getMessage()], 500);
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $businessId = $this->getValidatedBusinessId($request);

            $legalStructure = LegalStructure::with('tasks')
                ->where('business_id', $businessId)
                ->where('user_id', Auth::id())
                ->findOrFail($id);

            return response()->json($legalStructure);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Record not found'
            ], 404);
        }
    }

    // public function update(Request $request, $id)
    // {
    //     $businessId = $this->getValidatedBusinessId(request());

    //     $legalStructure = LegalStructure::where('id', $id)
    //     ->where('business_id', $businessId)
    //     ->where('user_id', Auth::id())
    //     ->firstOrFail();

    //     $data = $request->validate([
    //         'type' => 'nullable|string|max:255',
    //         'notes' => 'nullable|array',
    //         'tasks' => 'nullable|array',
    //         'tasks.*.id' => 'nullable|exists:legal_requirement_tasks,id',
    //         'tasks.*.title' => 'nullable|string|max:255',
    //         'tasks.*.status' => 'nullable|string|max:50',
    //         'tasks.*.deadline' => 'nullable|date',
    //     ]);

    //     DB::beginTransaction();
    //     try {
    //         $legalStructure->update($data);

    //         if (isset($data['tasks'])) {
    //             foreach ($data['tasks'] as $taskData) {
    //                 if (isset($taskData['id'])) {
    //                     $task = LegalRequirementTask::where('legal_structure_id', $legalStructure->id)
    //                         ->findOrFail($taskData['id']);
    //                     $task->update($taskData);
    //                 } else {
    //                     $taskData['legal_structure_id'] = $legalStructure->id;
    //                     LegalRequirementTask::create($taskData);
    //                 }
    //             }
    //         }

    //         DB::commit();
    //         return response()->json($legalStructure->fresh('tasks'));
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json(['error' => 'Failed to update record', 'details' => $e->getMessage()], 500);
    //     }
    // }
    public function update(Request $request, $id)
    {
        $businessId = $this->getValidatedBusinessId(request());

        $legalStructure = LegalStructure::where('id', $id)
            ->with('tasks')
            ->where('business_id', $businessId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $data = $request->validate([
            'type' => 'nullable|string|max:255',
            'notes' => 'nullable|array',
            'tasks' => 'nullable|array',
            'tasks.*.id' => 'nullable|exists:legal_requirement_tasks,id',
            'tasks.*.title' => 'nullable|string|max:255',
            'tasks.*.status' => 'nullable|string|max:50',
            'tasks.*.deadline' => 'nullable|date',
        ]);

        DB::beginTransaction();
        try {

            $legalStructure->update($data);


            $legalStructure->tasks()->delete();


            if (isset($data['tasks'])) {
                $submittedTaskIds = [];

                foreach ($data['tasks'] as $taskData) {

                    $newTask = LegalRequirementTask::create([
                        'legal_structure_id' => $legalStructure->id,
                        'title' => $taskData['title'],
                        'status' => $taskData['status'],
                        'deadline' => $taskData['deadline']
                    ]);

                    $submittedTaskIds[] = $newTask->id;
                }
            }

            DB::commit();
            return response()->json($legalStructure->fresh('tasks'));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to update record',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        $businessId = $this->getValidatedBusinessId(request());


        $legalStructure = LegalStructure::where('id', $id)
        ->where('business_id', $businessId)
        ->where('user_id', Auth::id())
        ->firstOrFail();


        $legalStructure->tasks()->delete();
        $legalStructure->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }


    public function updateProgress(Request $request)
    {
        $request->validate([
            'progress' => 'required|boolean',
        ]);

        $businessId = $this->getValidatedBusinessId($request);

      $legalStructure = LegalStructure::where('user_id', Auth::id())
            ->where('business_id', $businessId)
            ->latest()
            ->first();

        DB::beginTransaction();
        try {
            if (!$legalStructure) {
                $legalStructure = LegalStructure::create([
                    'user_id' => Auth::id(),
                    'business_id' => $businessId,
                    'progress' => $request->progress,
                    'type' => null,
                    'notes' => []
                ]);
  $legalStructure->tasks()->create([
                    'title' => 'Default legal requirement task',
                    'status' => 'pending',
                    'deadline' => null,
                    'progress' => $request->progress
                ]);

                DB::commit();

                return response()->json([
                    'message' => 'Legal structure record created with progress updated',
                    'data' => $legalStructure->load('tasks')
                ], 201);
            }

           $legalStructure->update(['progress' => $request->progress]);

            $legalStructure->tasks()->update(['progress' => $request->progress]);

            DB::commit();

            return response()->json([
                'message' => 'Progress updated successfully',
                'data' => $legalStructure->load('tasks')
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Progress update failed',
                'details' => $e->getMessage()
            ], 500);
        }
    }


    private function getValidatedBusinessId(Request $request)
    {
        try {
            $businessId = $request->header('business_id');

            if (!$businessId) {
                throw new \Exception('Missing business_id header', 422);
            }

            $business = Business::where('id', $businessId)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            return $businessId;

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode() ?? 403);
        }
    }
}
