<?php

namespace App\Http\Controllers;




use App\Models\BusinessIdea;
use App\Models\ConversionRate;
use App\Models\MarketResearch;
use App\Models\SimpleSolution;
use App\Models\TestingYourIdea;

use App\Models\FinancialPlanner;
use App\Models\LegalStructure;
use App\Models\Website;
use App\Models\ProductFeature;
use App\Models\Video;

class DashboardController extends Controller
{
    protected function getSafeSum($collection, $column)
    {
        if (!$collection || $collection->isEmpty()) {
            return 0;
        }

        return $collection->sum(function ($item) use ($column) {
            return is_numeric($item->$column) ? $item->$column : 0;
        });
    }

    protected function getSafePercentage($part, $total, $decimals = 1)
    {
        if (!is_numeric($part) || !is_numeric($total) || $total == 0) {
            return 0;
        }
        return number_format(($part / $total) * 100, $decimals);
    }
    public function index()
    {
        $last12Months = collect();
        $plannedFundingData = collect();
        $securedFundingData = collect();

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $last12Months->push($date->format('M Y'));


            $plannedFundingData->push(rand(5000, 15000));
            $securedFundingData->push(rand(3000, $plannedFundingData->last()));
        }
        $startupCosts = SimpleSolution::all();
        $fundingSources = FinancialPlanner::all();
        $videos = Video::all();

        $stats = [
            'videos' => $videos,
            'startupCosts' => $startupCosts,
            'fundingSources' => $fundingSources,
            'salesStrategies' => ConversionRate::all(),

            // 'mvpDevelopments' => MVPDevelopment::all(),
            'last12Months' => $last12Months,
            'plannedFundingData' => $plannedFundingData,
            'securedFundingData' => $securedFundingData,

            'businessIdeas' => BusinessIdea::count(),
            'salesStrategiesCount' => ConversionRate::count(),
            'marketingNews' => ProductFeature::count(),
            'marketResearches' => MarketResearch::count(),
            'startSimples' => SimpleSolution::count(),
            'testingIdeas' => TestingYourIdea::count(),
            'businessSetups' => LegalStructure::count(),
            'financialPlanners' => FinancialPlanner::count(),
            'websites' => Website::count(),


            'averageBreakeven' => $this->calculateAverageBreakeven(),
            'plannedFunding' => $this->calculatePlannedFunding(),
            'securedFunding' => $this->safeSum($fundingSources, 'amount'),
            'securedFundingPercentage' => $this->calculateSecuredFundingPercentage(),
            'pendingFunding' => $this->calculatePendingFunding(),
            'pendingFundingPercentage' => $this->calculatePendingFundingPercentage(),


            'safeSum' => [$this, 'safeSum'],
            'safePercentage' => [$this, 'safePercentage'],
        ];
        // dd($stats);
        return view('dashboard', $stats);
    }


    protected function safeSum($collection, $column)
    {
        return $collection->sum(function ($item) use ($column) {
            return is_numeric($item->$column) ? $item->$column : 0;
        });
    }


    protected function safePercentage($part, $total, $decimals = 1)
    {
        if (!is_numeric($part) || !is_numeric($total) || $total == 0) {
            return 0;
        }
        return number_format(($part / $total) * 100, $decimals);
    }

    protected function calculateAverageBreakeven()
    {

        return number_format(12, 1);
    }

    protected function calculatePlannedFunding()
    {

        $planned = 50000;
        return is_numeric($planned) ? $planned : 0;
    }

    protected function calculateSecuredFundingPercentage()
    {
        $secured = $this->safeSum(FinancialPlanner::all(), 'amount');
        $planned = $this->calculatePlannedFunding();

        return $planned > 0 ? round(($secured / $planned) * 100) : 0;
    }

    protected function calculatePendingFunding()
    {
        $planned = $this->calculatePlannedFunding();
        $secured = $this->safeSum(FinancialPlanner::all(), 'amount');

        return max(0, $planned - $secured);
    }

    protected function calculatePendingFundingPercentage()
    {
        return 100 - $this->calculateSecuredFundingPercentage();
    }
}
