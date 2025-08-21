<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BusinessIdea;
use App\Models\BusinessSetup;
use App\Models\ConversionRate;
use App\Models\FinancialPlanner;
use App\Models\FinancialPlanning;
use App\Models\LaunchPreparation;
use App\Models\LegalStructure;
use App\Models\Marketing;
use App\Models\MarketResearch;
use App\Models\MVPDevelopment;
use App\Models\ProductFeature;
use App\Models\SalesStrategy;
use App\Models\SimpleSolution;
use App\Models\TestingYourIdea;
use App\Models\Website;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\Response;

class DownloadController extends Controller
{
    public function downloadBusinessData(Request $request)
    {
        $userId = Auth::id();
$name=Auth::user()->name;
        $businessId = $this->getValidatedBusinessId($request);
        $business = Business::where('id', $businessId)
            ->where('user_id', Auth::id())->select('id', 'name')
            ->first();
        $latestIdea = BusinessIdea::where('user_id', $userId)->where('business_id', $businessId)->latest()->first();
        $ideas = TestingYourIdea::where('user_id', Auth::id())
            ->where('business_id', $businessId)
            ->get();

       $latestResearch = MarketResearch::where('user_id', $userId)->where('business_id', $businessId)->latest()->first();

       $latest = SimpleSolution::where('user_id', Auth::id())
       ->where('business_id', $businessId)
       ->latest()
       ->first();



       $latestMarketing = ProductFeature::with('marketingCampaigns')
       ->where('user_id', auth()->id())
       ->where('business_id', $businessId)
       ->latest()
       ->first();


       $rates = ConversionRate::where('user_id', Auth::id())
       ->where('business_id', $businessId)
       ->latest()
       ->get();


     $testSetup = LegalStructure::where('user_id', $userId)->where('business_id', $businessId)->with(['tasks'])->latest()->first();



     $latestPlanning=FinancialPlanner::where('business_id', $businessId)
     ->where('user_id', Auth::id())
     ->latest()
     ->first();



        $website =Website::where('business_id', $businessId)
        ->where('user_id', Auth::id())
            ->with('services')
            ->latest()
                ->first();


        $data = [
            'name'=>$name,
            'landing_page' => $business->name,
            'latest_business_idea' => $latestIdea,
            'testing_your_idea' => $ideas,
            'test_setup' => $testSetup,
            'latest_financial_planning' => $latestPlanning,

            'latest_marketing' => $latestMarketing,
            'latest_market_research' => $latestResearch,
            'start_simple' => $latest,
            'latest_sales_strategy' => $rates,
            'website'=>$website,
        ];

        $jsonData = json_encode($data, JSON_PRETTY_PRINT);


        $fileName = 'business_data_' . date('Y-m-d_H-i-s') . '.json';


        return new StreamedResponse(function () use ($jsonData) {
            echo $jsonData;
        }, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
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
