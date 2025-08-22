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
    public function index()
    {
        $userId = Auth::id();
        $businesses = Business::where('user_id', $userId)
            ->with('user:id,name,email')
            ->select('id', 'name', 'user_id')
            ->get();

        // حساب حالة التقدم لكل business
        $businesses->each(function ($business) use ($userId) {
            $progressResult = $this->calculateBusinessProgress($business->id, $userId);
            $business->progress = $progressResult['has_progress'];
            $business->progress_details = $progressResult['details'];
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

        // حساب حالة التقدم ودمج البيانات
        $progressResult = $this->calculateBusinessProgress($business->id, $userId);
        $business->progress = $progressResult['has_progress'];
        $business->progress_details = $progressResult['details'];

        return response()->json([
            'success' => true,
            'data' => $business
        ], 200);
    }

    /**
     * حساب تقدم Business معين (يعيد true/false لكل قسم)
     */
    private function calculateBusinessProgress($businessId, $userId)
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

        $hasAnyProgress = false;
        $details = [];

        foreach ($models as $key => $model) {
            // التحقق من وجود بيانات للنموذج
            $hasData = $model::where('user_id', $userId)
                ->where('business_id', $businessId)
                ->exists();

            if ($hasData) {
                $hasAnyProgress = true;
            }

            $details[$key] = $hasData;
        }

        return [
            'has_progress' => $hasAnyProgress,
            'details' => $details
        ];
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
