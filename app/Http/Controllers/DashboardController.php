<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\User;
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
        return $collection->sum(fn ($item) => is_numeric($item->$column) ? $item->$column : 0);
    }

    protected function getSafePercentage($part, $total, $decimals = 1)
    {
        if (!is_numeric($part) || !is_numeric($total) || $total == 0) return 0;
        return number_format(($part / $total) * 100, $decimals);
    }

    public function index()
    {
        // بيانات الخطوط الوهمية كما هي
        $last12Months = collect();
        $plannedFundingData = collect();
        $securedFundingData = collect();
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $last12Months->push($date->format('M Y'));
            $plannedFundingData->push(rand(5000, 15000));
            $securedFundingData->push(rand(3000, $plannedFundingData->last()));
        }

        // لو أدمن: نفس السلوك الحالي
        if (auth()->user()->isAdmin() || auth()->user()->hasRole('admin')) {
            $startupCosts   = SimpleSolution::all();
            $fundingSources = FinancialPlanner::all();
            $videos         = Video::all();

            $stats = [
                'videos'               => $videos,
                'startupCosts'         => $startupCosts,
                'fundingSources'       => $fundingSources,
                'salesStrategies'      => ConversionRate::all(),
                'last12Months'         => $last12Months,
                'plannedFundingData'   => $plannedFundingData,
                'securedFundingData'   => $securedFundingData,
                'businessIdeas'        => BusinessIdea::count(),
                'salesStrategiesCount' => ConversionRate::count(),
                'marketingNews'        => ProductFeature::count(),
                'marketResearches'     => MarketResearch::count(),
                'startSimples'         => SimpleSolution::count(),
                'testingIdeas'         => TestingYourIdea::count(),
                'businessSetups'       => LegalStructure::count(),
                'financialPlanners'    => FinancialPlanner::count(),
                'websites'             => Website::count(),

                'averageBreakeven'           => $this->calculateAverageBreakeven(),
                'plannedFunding'             => $this->calculatePlannedFunding(),
                'securedFunding'             => $this->safeSum($fundingSources, 'amount'),
                'securedFundingPercentage'   => $this->calculateSecuredFundingPercentage(),
                'pendingFunding'             => $this->calculatePendingFunding(),
                'pendingFundingPercentage'   => $this->calculatePendingFundingPercentage(),

                'safeSum'        => [$this, 'safeSum'],
                'safePercentage' => [$this, 'safePercentage'],
            ];

            return view('dashboard', $stats);
        }

        // -------------------------
        // مستخدم ذو أدوار (Role-based)
        // -------------------------
        $myRegionIds = $this->getMyRegionIds();
        // لو ما عنده مناطق، نرجّع أرقام صفرية بشكل أنيق
        if (empty($myRegionIds)) {
            $emptyStats = [
                'videos'               => collect(),
                'startupCosts'         => collect(),
                'fundingSources'       => collect(),
                'salesStrategies'      => collect(),
                'last12Months'         => $last12Months,
                'plannedFundingData'   => $plannedFundingData,
                'securedFundingData'   => $securedFundingData,

                'usersInMyRegions'     => 0,
                'filledCounts'         => [
                    'businessIdeas'     => ['records' => 0, 'users' => 0],
                    'marketResearches'  => ['records' => 0, 'users' => 0],
                    'marketingNews'     => ['records' => 0, 'users' => 0],
                    'salesStrategies'   => ['records' => 0, 'users' => 0],
                    'startSimples'      => ['records' => 0, 'users' => 0],
                    'testingIdeas'      => ['records' => 0, 'users' => 0],
                    'businessSetups'    => ['records' => 0, 'users' => 0],
                    'financialPlanners' => ['records' => 0, 'users' => 0],
                    'websites'          => ['records' => 0, 'users' => 0],
                ],

                'averageBreakeven'         => $this->calculateAverageBreakeven(),
                'plannedFunding'           => $this->calculatePlannedFunding(),
                'securedFunding'           => 0,
                'securedFundingPercentage' => 0,
                'pendingFunding'           => $this->calculatePlannedFunding(),
                'pendingFundingPercentage' => 100,

                'safeSum'        => [$this, 'safeSum'],
                'safePercentage' => [$this, 'safePercentage'],
            ];
            return view('dashboard', $emptyStats);
        }

        // إجمالي المستخدمين ضمن مناطقي
        $usersInMyRegions = User::whereHas('migrantProfile', function ($q) use ($myRegionIds) {
            $q->whereIn('region_id', $myRegionIds);
        })->count();

        // قيود المناطق لكل الوحدات
        $startupCosts   = SimpleSolution::whereHas('user.migrantProfile', fn($q) => $q->whereIn('region_id', $myRegionIds))->get();
        $fundingSources = FinancialPlanner::whereHas('user.migrantProfile', fn($q) => $q->whereIn('region_id', $myRegionIds))->get();

        // عدادات “كم سجل” و “كم مستخدم مميز” لكل وحدة
        $filledCounts = [
            'businessIdeas'     => $this->countsFor(BusinessIdea::class, $myRegionIds),
            'marketResearches'  => $this->countsFor(MarketResearch::class, $myRegionIds),
            'marketingNews'     => $this->countsFor(ProductFeature::class, $myRegionIds),
            'salesStrategies'   => $this->countsFor(ConversionRate::class, $myRegionIds),
            'startSimples'      => $this->countsFor(SimpleSolution::class, $myRegionIds),
            'testingIdeas'      => $this->countsFor(TestingYourIdea::class, $myRegionIds),
            'businessSetups'    => $this->countsFor(LegalStructure::class, $myRegionIds),
            'financialPlanners' => $this->countsFor(FinancialPlanner::class, $myRegionIds),
            'websites'          => $this->countsFor(Website::class, $myRegionIds),
        ];

        // أرقام التمويل (مقيّدة بالمناطق)
        $securedFunding = $this->safeSum($fundingSources, 'amount');
        $plannedFunding = $this->calculatePlannedFunding(); // إن أردتها مقيّدة بالمناطق عدّلها

        $stats = [
            'videos'               => Video::all(), // إن أردته مقيّدًا بالمناطق: فلتره مثل الباقي
            'startupCosts'         => $startupCosts,
            'fundingSources'       => $fundingSources,
            'salesStrategies'      => ConversionRate::whereHas('user.migrantProfile', fn($q) => $q->whereIn('region_id', $myRegionIds))->get(),

            'last12Months'         => $last12Months,
            'plannedFundingData'   => $plannedFundingData,
            'securedFundingData'   => $securedFundingData,

            'usersInMyRegions'     => $usersInMyRegions,
            'filledCounts'         => $filledCounts,

            // إن أردت أيضًا الإجماليات كأرقام خام لكل وحدة مقيّدة بالمناطق:
            'businessIdeas'        => $filledCounts['businessIdeas']['records'],
            'salesStrategiesCount' => $filledCounts['salesStrategies']['records'],
            'marketingNews'        => $filledCounts['marketingNews']['records'],
            'marketResearches'     => $filledCounts['marketResearches']['records'],
            'startSimples'         => $filledCounts['startSimples']['records'],
            'testingIdeas'         => $filledCounts['testingIdeas']['records'],
            'businessSetups'       => $filledCounts['businessSetups']['records'],
            'financialPlanners'    => $filledCounts['financialPlanners']['records'],
            'websites'             => $filledCounts['websites']['records'],

            'averageBreakeven'           => $this->calculateAverageBreakeven(),
            'plannedFunding'             => $plannedFunding,
            'securedFunding'             => $securedFunding,
            'securedFundingPercentage'   => $plannedFunding > 0 ? round(($securedFunding / $plannedFunding) * 100) : 0,
            'pendingFunding'             => max(0, $plannedFunding - $securedFunding),
            'pendingFundingPercentage'   => $plannedFunding > 0 ? 100 - round(($securedFunding / $plannedFunding) * 100) : 0,

            'safeSum'        => [$this, 'safeSum'],
            'safePercentage' => [$this, 'safePercentage'],
        ];

        return view('dashboard', $stats);
    }

    // ------- Helpers إضافية -------

    private function getMyRegionIds(): array
    {
        return auth()->user()->roles()
            ->pluck('region_id')
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    /**
     * countsFor: تُعيد ['records' => totalRecords, 'users' => distinctUsers] مقيّدة بالمناطق
     */
    private function countsFor(string $modelClass, array $regionIds): array
    {
        $query = $modelClass::whereHas('user.migrantProfile', function ($q) use ($regionIds) {
            $q->whereIn('region_id', $regionIds);
        });

        $records = (clone $query)->count();
        $users   = (clone $query)->distinct('user_id')->count('user_id');

        return ['records' => $records, 'users' => $users];
    }

    // ====== بقية دوالك كما هي ======

    protected function safeSum($collection, $column)
    {
        return $collection->sum(fn ($item) => is_numeric($item->$column) ? $item->$column : 0);
    }

    protected function safePercentage($part, $total, $decimals = 1)
    {
        if (!is_numeric($part) || !is_numeric($total) || $total == 0) return 0;
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
