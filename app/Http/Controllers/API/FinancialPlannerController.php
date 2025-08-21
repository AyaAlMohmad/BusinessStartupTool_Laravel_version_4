<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\FinancialPlanner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FinancialPlannerController extends Controller
{

    public function index(Request $request)
    {
        try {
            $businessId = $this->validateBusiness($request);

            $data = FinancialPlanner::where('business_id', $businessId)
                ->where('user_id', Auth::id())
                ->latest()
                ->first();

            return response()->json($data ?? null);
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }
    public function show(Request $request, $id)
    {
        try {
            $businessId = $this->validateBusiness($request);

            $data = FinancialPlanner::where('business_id', $businessId)->where('id', $id)
                ->where('user_id', Auth::id())
                ->first();

            return response()->json($data ?? null);
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function store(Request $request)
    {
        try {
            $businessId = $this->validateBusiness($request);
            $userId = Auth::id();


            $validator = Validator::make($request->all(), [
                // 'operational_details' => 'required|array',
                'notes' => 'nullable|array',
                // 'excel_file' => 'nullable|file|mimes:xlsx,xls|max:5120'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->errors()
                ], 422);
            }

            $data = $request->all();
            // $data = $request->except('excel_file');
            $data['user_id'] = $userId;
            $data['business_id'] = $businessId;


            // if ($request->hasFile('excel_file')) {
            //     $file = $request->file('excel_file');
            //     $path = $file->store('financial-planners');
            //     $data['excel_file'] = $path;
            // }


            $planner = FinancialPlanner::updateOrCreate(
                // [
                //     'business_id' => $businessId,
                //     'user_id' => $userId
                // ],
                $data
            );

            return response()->json($planner, 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }
    public function update(Request $request, $id)
    {
        try {

            $planner = FinancialPlanner::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();


            $validator = Validator::make($request->all(), [
                // 'operational_details' => 'sometimes|array',
                'notes' => 'nullable|array',
                // 'excel_file' => 'nullable|file|mimes:xlsx,xls|max:5120'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->errors()
                ], 422);
            }

            // $data = $request->except('excel_file');
            $data = $request->all();

            // if ($request->hasFile('excel_file')) {

            //     if ($planner->excel_file) {
            //         Storage::delete($planner->excel_file);
            //     }

            //     $file = $request->file('excel_file');
            //     $path = $file->store('financial-planners');
            //     $data['excel_file'] = $path;
            // }

            $planner->update($data);

            return response()->json($planner, 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Financial planner not found or unauthorized'
            ], 404);
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }


    public function downloadFile(Request $request)
    {
        try {
            $businessId = $this->validateBusiness($request);

            $planner = FinancialPlanner::where('business_id', $businessId)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            if (!$planner->excel_file || !Storage::exists($planner->excel_file)) {
                throw new \Exception('File not found', 404);
            }

            return Storage::download($planner->excel_file);
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }


    public function destroy(Request $request)
    {
        try {
            $businessId = $this->validateBusiness($request);

            $planner = FinancialPlanner::where('business_id', $businessId)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            // if ($planner->excel_file) {
            //     Storage::delete($planner->excel_file);
            // }

            $planner->delete();

            return response()->json(['message' => 'Deleted successfully']);
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }


    public function updateProgress(Request $request)
    {
        try {
            $request->validate([
                'progress' => 'required|boolean',
            ]);

            $businessId = $this->validateBusiness($request);
            $userId = Auth::id();

            // البحث عن سجل FinancialPlanner الحالي
            $financialPlanner = FinancialPlanner::where('user_id', $userId)
                ->where('business_id', $businessId)
                ->latest()
                ->first();

            // إذا لم يوجد سجل، ننشئ واحداً جديداً
            if (!$financialPlanner) {
                $financialPlanner = FinancialPlanner::create([
                    'user_id' => $userId,
                    'business_id' => $businessId,
                    'progress' => $request->progress,
                    'operational_details' => [],
                    'notes' => [],
                    'excel_file' => null
                ]);

                return response()->json([
                    'message' => 'Financial planner record created with progress updated',
                    'data' => $financialPlanner
                ], 201);
            }

            // إذا وجد سجل، نحدث حقل progress فقط
            $financialPlanner->update(['progress' => $request->progress]);

            return response()->json([
                'message' => 'Progress updated successfully',
                'data' => $financialPlanner
            ], 200);

        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }


    private function validateBusiness(Request $request)
    {
        $businessId = $request->header('business-id');
        if (!$businessId) {
            throw new \Exception('Business ID required', 422);
        }

        $business = Business::where('id', $businessId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return $businessId;
    }

    private function errorResponse(\Exception $e)
    {
        $code = $e->getCode() ?: 500;
        return response()->json([
            'error' => $e->getMessage()
        ], $code);
    }
}
