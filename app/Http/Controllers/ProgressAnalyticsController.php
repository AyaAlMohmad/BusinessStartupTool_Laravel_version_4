<?php

namespace App\Http\Controllers;

use App\Models\ProductFeature;
use App\Models\User;
use App\Models\Business;
use App\Models\BusinessIdea;
use App\Models\ConversionRate;
use App\Models\FinancialPlanner;
use App\Models\LegalStructure;
use App\Models\MarketResearch;
use App\Models\RoleUser;
use App\Models\SimpleSolution;
use App\Models\TestingYourIdea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProgressAnalyticsController extends Controller
{
    /** أدوار لها صلاحية مشاهدة كل البيانات */
    protected array $fullAccessRoleNames = ['Admin', 'Super Admin'];

    protected function countOrZero(string $modelClass, ?array $userIds = null): int
    {
        if (!class_exists($modelClass)) return 0;

        $query = $modelClass::query();

        if ($userIds !== null && Schema::hasColumn((new $modelClass)->getTable(), 'user_id')) {
            $query->whereIn('user_id', $userIds);
        }

        return (int) $query->count();
    }

    /** تحديد نطاق المستخدمين المسموح برؤية بياناتهم */
    protected function getScopedUserIds(): ?array
    {
        $user = Auth::user();
        if (!$user) return [];

        // التحقق إذا كان المستخدم لديه دور من الأدوار المميزة
        $hasFullAccess = $user->roles()
            ->whereIn('name', $this->fullAccessRoleNames)
            ->exists();

        if ($hasFullAccess) {
            return null; // null يعني عرض جميع البيانات
        }

        // جلب جميع المستخدمين الذين لديهم نفس أدوار المستخدم الحالي
        $userRoleIds = $user->roles()->pluck('roles.id');

        $scopedUserIds = RoleUser::whereIn('role_id', $userRoleIds)
            ->pluck('user_id')
            ->unique()
            ->toArray();

        // التأكد من تضمين المستخدم الحالي حتى لو لم يكن له دور
        if (!in_array($user->id, $scopedUserIds)) {
            $scopedUserIds[] = $user->id;
        }

        return $scopedUserIds;
    }

    public function index()
    {
        $scopedUserIds = $this->getScopedUserIds();
        $totalRecords = 100; // يمكن تعديلها حسب الحاجة

        // حساب نسب التقدم مع مراعاة نطاق المستخدمين
        $sectionCompletion = [
            'business_idea' => round(($this->countOrZero(BusinessIdea::class, $scopedUserIds) / $totalRecords) * 100),
            'market_research' => round(($this->countOrZero(MarketResearch::class, $scopedUserIds) / $totalRecords) * 100),
            'marketing' => round(($this->countOrZero(ProductFeature::class, $scopedUserIds) / $totalRecords) * 100),
            'mvp_development' => round(($this->countOrZero(SimpleSolution::class, $scopedUserIds) / $totalRecords) * 100),
            'sales' => round(($this->countOrZero(TestingYourIdea::class, $scopedUserIds) / $totalRecords) * 100),
            'business_setup' => round(($this->countOrZero(ConversionRate::class, $scopedUserIds) / $totalRecords) * 100),
            'financial_planning' => round(($this->countOrZero(FinancialPlanner::class, $scopedUserIds) / $totalRecords) * 100),
            'launch_preparation' => round(($this->countOrZero(LegalStructure::class, $scopedUserIds) / $totalRecords) * 100),
        ];

        // نشاط المستخدمين (المستخدمون المصرح لهم فقط)
        $userActivityQuery = User::query();

        if ($scopedUserIds !== null) {
            $userActivityQuery->whereIn('id', $scopedUserIds);
        }

        $userActivity = [
            'last_24_hours' => $userActivityQuery->clone()
                ->whereDate('last_login', now()->toDateString())
                ->count(),
            'last_7_days' => $userActivityQuery->clone()
                ->whereDate('last_login', '>=', now()->subDays(7))
                ->count(),
            'last_30_days' => $userActivityQuery->clone()
                ->whereDate('last_login', '>=', now()->subDays(30))
                ->count(),
        ];

        // أكثر الأقسام نشاطًا (حسب نطاق المستخدمين)
        $mostActiveSections = [
            'business_idea' => $this->countOrZero(BusinessIdea::class, $scopedUserIds),
            'market_research' => $this->countOrZero(MarketResearch::class, $scopedUserIds),
            'marketing' => $this->countOrZero(ProductFeature::class, $scopedUserIds),
            'mvp_development' => $this->countOrZero(SimpleSolution::class, $scopedUserIds),
        ];

        // حساب الفئات العمرية مع مراعاة نطاق المستخدمين
        $currentYear = now()->year;
        $ageQuery = \App\Models\MigrantProfile::query()
            ->whereNotNull('birth_year');

        if ($scopedUserIds !== null) {
            $ageQuery->whereIn('user_id', $scopedUserIds);
        }

        $ageGroups = [
            'under_18' => $ageQuery->clone()->where('birth_year', '>=', $currentYear - 17)->count(),
            '18_25' => $ageQuery->clone()->whereBetween('birth_year', [$currentYear - 25, $currentYear - 18])->count(),
            '26_35' => $ageQuery->clone()->whereBetween('birth_year', [$currentYear - 35, $currentYear - 26])->count(),
            '36_45' => $ageQuery->clone()->whereBetween('birth_year', [$currentYear - 45, $currentYear - 36])->count(),
            '46_55' => $ageQuery->clone()->whereBetween('birth_year', [$currentYear - 55, $currentYear - 46])->count(),
            'over_55' => $ageQuery->clone()->where('birth_year', '<', $currentYear - 55)->count(),
        ];

        // إحصائيات النشاط حسب الوقت (للمستخدمين المصرح لهم فقط)
        $activityByTimeQuery = User::query();

        if ($scopedUserIds !== null) {
            $activityByTimeQuery->whereIn('id', $scopedUserIds);
        }

        $activityByTime = [
            'morning' => $activityByTimeQuery->clone()
                ->whereTime('last_login', '>=', '06:00:00')
                ->whereTime('last_login', '<', '12:00:00')
                ->count(),
            'afternoon' => $activityByTimeQuery->clone()
                ->whereTime('last_login', '>=', '12:00:00')
                ->whereTime('last_login', '<', '18:00:00')
                ->count(),
            'evening' => $activityByTimeQuery->clone()
                ->whereTime('last_login', '>=', '18:00:00')
                ->whereTime('last_login', '<', '24:00:00')
                ->count(),
            'night' => $activityByTimeQuery->clone()
                ->whereTime('last_login', '>=', '00:00:00')
                ->whereTime('last_login', '<', '06:00:00')
                ->count(),
        ];

        // إحصائيات إضافية من MigrantProfile (مع مراعاة نطاق المستخدمين)
        $migrantQuery = \App\Models\MigrantProfile::query();

        if ($scopedUserIds !== null) {
            $migrantQuery->whereIn('user_id', $scopedUserIds);
        }

        $migrantStats = [
            'cultural_background' => $migrantQuery->clone()
                ->select('cultural_background', DB::raw('count(*) as total'))
                ->whereNotNull('cultural_background')
                ->groupBy('cultural_background')
                ->pluck('total', 'cultural_background'),
            'visa_category' => $migrantQuery->clone()
                ->select('visa_category', DB::raw('count(*) as total'))
                ->whereNotNull('visa_category')
                ->groupBy('visa_category')
                ->pluck('total', 'visa_category'),
            'business_stage' => $migrantQuery->clone()
                ->select('business_stage', DB::raw('count(*) as total'))
                ->whereNotNull('business_stage')
                ->groupBy('business_stage')
                ->pluck('total', 'business_stage'),
            'languages' => $migrantQuery->clone()
                ->select('languages', DB::raw('count(*) as total'))
                ->whereNotNull('languages')
                ->groupBy('languages')
                ->pluck('total', 'languages'),
            'arrival_year' => $migrantQuery->clone()
                ->select('arrival_year', DB::raw('count(*) as total'))
                ->whereNotNull('arrival_year')
                ->groupBy('arrival_year')
                ->orderBy('arrival_year')
                ->pluck('total', 'arrival_year'),
        ];

        return view('ProgressAnalytics', compact(
            'sectionCompletion',
            'userActivity',
            'mostActiveSections',
            'ageGroups',
            'activityByTime',
            'migrantStats'
        ));
    }
}
