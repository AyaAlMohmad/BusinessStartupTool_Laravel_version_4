<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Business;
use App\Models\BusinessIdea;
use App\Models\ConversionRate;
use App\Models\FinancialPlanner;
use App\Models\LegalStructure;
use App\Models\MarketResearch;
use App\Models\ProductFeature;
use App\Models\SimpleSolution;
use App\Models\TestingYourIdea;
use App\Models\Website;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class BusinessController extends Controller
{

    // public function index()
    // {
    //     $businesses = Business::where('user_id', auth()->id())
    //         ->with('user:id,name,email')
    //         ->select('id', 'name', 'user_id')
    //         ->get();

    //     return response()->json([
    //         'success' => true,
    //         'data' => $businesses
    //     ], 200);
    // }


    // public function show($id)
    // {
    //     $business = Business::where('id', $id)
    //         ->where('user_id', auth()->id())
    //         ->with('user:id,name,email')
    //         ->select('id', 'name', 'user_id')
    //         ->firstOrFail();

    //     return response()->json([
    //         'success' => true,
    //         'data' => $business
    //     ], 200);
    // }

    public function index()
    {
        $userId = Auth::id();
        $businesses = Business::where('user_id', $userId)
            ->with('user:id,name,email')
            ->select('id', 'name', 'user_id')
            ->get();

        // حساب التقدم لكل business
        $businesses->each(function ($business) use ($userId) {
            $business->progress = $this->calculateBusinessProgress($business->id, $userId);
        });

        return response()->json([
            'success' => true,
            'data' => $businesses
        ], 200);
    }

    /**
     * عرض Business معين مع تفاصيل تقدمه
     */
    public function show($id)
    {
        $userId = Auth::id();
        $business = Business::where('id', $id)
            ->where('user_id', $userId)
            ->with('user:id,name,email')
            ->select('id', 'name', 'user_id')
            ->firstOrFail();

        // حساب التقدم ودمج البيانات
        $progressDetails = $this->calculateBusinessProgress($business->id, $userId, true);
        $business->progress = $progressDetails['total_progress'];
        $business->progress_details = $progressDetails['details'];

        return response()->json([
            'success' => true,
            'data' => $business
        ], 200);
    }

    /**
     * حساب تقدم Business معين
     */
    private function calculateBusinessProgress($businessId, $userId, $withDetails = false)
    {
        // النماذج المراد تتبع تقدمها
        $models = [
            'business_idea' => BusinessIdea::class,
            'testing_idea' => TestingYourIdea::class,
            'market_research' => MarketResearch::class,
            'simple_solution' => SimpleSolution::class,
            'product_feature' => ProductFeature::class,
            'conversion_rate' => ConversionRate::class,
            'legal_structure' => LegalStructure::class,
            'financial_planner' => FinancialPlanner::class,
            'website' => Website::class,
        ];

        $totalWeight = 0;
        $totalProgress = 0;
        $details = [];

        foreach ($models as $key => $model) {
            // تحديد الوزن لكل نموذج (يمكن تعديله حسب الأهمية)
            $weight = 1;

            // التحقق من وجود بيانات للنموذج
            $hasData = $model::where('user_id', $userId)
                ->where('business_id', $businessId)
                ->exists();

            // حساب التقدم لهذا النموذج
            $progress = $hasData ? 100 : 0;

            $totalWeight += $weight;
            $totalProgress += $progress * $weight;

            if ($withDetails) {
                $details[$key] = [
                    'has_data' => $hasData,
                    'progress' => $progress,
                    'weight' => $weight
                ];
            }
        }

        // حساب التقدم الكلي
        $totalProgress = $totalWeight > 0 ? round($totalProgress / $totalWeight, 2) : 0;

        if ($withDetails) {
            return [
                'total_progress' => $totalProgress,
                'details' => $details
            ];
        }

        return $totalProgress;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
        ]);

        $validated['user_id'] = auth()->id();

        $business = Business::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Business created successfully',
            'data' => $business
        ], Response::HTTP_CREATED);
    }


    public function update(Request $request, $businessId)
    {
        $business = Business::where('id', $businessId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
        ]);

        $business->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Business updated successfully',
            'data' => $business
        ], 200);
    }


    public function destroy($businessId)
    {
        $business = Business::where('id', $businessId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $business->delete();

        return response()->json([
            'success' => true,
            'message' => 'Business deleted successfully'
        ], 200);
    }


    public function showLogs($businessId)
    {
        $business = Business::where('id', $businessId)
            ->where('user_id', auth()->id())
            ->select('id', 'name', 'user_id')
            ->firstOrFail();

        $logs = AuditLog::where('table_name', 'businesses')
            ->where('record_id', $business->id)
            ->with('user:id,name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'business' => $business,
                'logs' => $logs
            ]
        ], 200);
    }
    private function getValidatedBusinessId(Request $request)
    {
       
        return $request->business_id;
    }
}
