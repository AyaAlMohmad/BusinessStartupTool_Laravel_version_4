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
        $scopedUserIds  = $this->getScopedUserIds(); // null = كل الموقع
        $isFullAccess   = ($scopedUserIds === null);
        $totalRecords   = 100;

        /* نسب التقدم (محكومة بالسكوپ) */
        $sectionCompletion = [
            'business_idea'      => round(($this->countOrZero(\App\Models\BusinessIdea::class,     $scopedUserIds) / $totalRecords) * 100),
            'market_research'    => round(($this->countOrZero(\App\Models\MarketResearch::class,   $scopedUserIds) / $totalRecords) * 100),
            'marketing'          => round(($this->countOrZero(\App\Models\ProductFeature::class,   $scopedUserIds) / $totalRecords) * 100),
            'mvp_development'    => round(($this->countOrZero(\App\Models\SimpleSolution::class,   $scopedUserIds) / $totalRecords) * 100),
            'sales'              => round(($this->countOrZero(\App\Models\TestingYourIdea::class,  $scopedUserIds) / $totalRecords) * 100),
            'business_setup'     => round(($this->countOrZero(\App\Models\ConversionRate::class,   $scopedUserIds) / $totalRecords) * 100),
            'financial_planning' => round(($this->countOrZero(\App\Models\FinancialPlanner::class, $scopedUserIds) / $totalRecords) * 100),
            'launch_preparation' => round(($this->countOrZero(\App\Models\LegalStructure::class,   $scopedUserIds) / $totalRecords) * 100),
        ];

        $mostActiveSections = [
            'business_idea'   => $this->countOrZero(\App\Models\BusinessIdea::class,   $scopedUserIds),
            'market_research' => $this->countOrZero(\App\Models\MarketResearch::class, $scopedUserIds),
            'marketing'       => $this->countOrZero(\App\Models\ProductFeature::class, $scopedUserIds),
            'mvp_development' => $this->countOrZero(\App\Models\SimpleSolution::class, $scopedUserIds),
        ];

        /* نشاط المستخدمين */
        $userActivityQuery = \App\Models\User::query();
        if ($scopedUserIds !== null) $userActivityQuery->whereIn('id', $scopedUserIds);
        $userActivity = [
            'last_24_hours' => (clone $userActivityQuery)->whereDate('last_login', now()->toDateString())->count(),
            'last_7_days'   => (clone $userActivityQuery)->whereDate('last_login', '>=', now()->subDays(7))->count(),
            'last_30_days'  => (clone $userActivityQuery)->whereDate('last_login', '>=', now()->subDays(30))->count(),
        ];

        /* الفئات العمرية */
        $currentYear = now()->year;
        $migrantQuery = \App\Models\MigrantProfile::query()->whereNotNull('birth_year');
        if ($scopedUserIds !== null) $migrantQuery->whereIn('user_id', $scopedUserIds);

        $ageGroups = [
            'under_18' => (clone $migrantQuery)->where('birth_year', '>=', $currentYear - 17)->count(),
            '18_25'    => (clone $migrantQuery)->whereBetween('birth_year', [$currentYear - 25, $currentYear - 18])->count(),
            '26_35'    => (clone $migrantQuery)->whereBetween('birth_year', [$currentYear - 35, $currentYear - 26])->count(),
            '36_45'    => (clone $migrantQuery)->whereBetween('birth_year', [$currentYear - 45, $currentYear - 36])->count(),
            '46_55'    => (clone $migrantQuery)->whereBetween('birth_year', [$currentYear - 55, $currentYear - 46])->count(),
            'over_55'  => (clone $migrantQuery)->where('birth_year', '<',  $currentYear - 55)->count(),
        ];

        /* النشاط حسب الوقت */
        $activityByTimeQuery = \App\Models\User::query();
        if ($scopedUserIds !== null) $activityByTimeQuery->whereIn('id', $scopedUserIds);
        $activityByTime = [
            'morning'   => (clone $activityByTimeQuery)->whereTime('last_login','>=','06:00:00')->whereTime('last_login','<','12:00:00')->count(),
            'afternoon' => (clone $activityByTimeQuery)->whereTime('last_login','>=','12:00:00')->whereTime('last_login','<','18:00:00')->count(),
            'evening'   => (clone $activityByTimeQuery)->whereTime('last_login','>=','18:00:00')->whereTime('last_login','<=','23:59:59')->count(),
            'night'     => (clone $activityByTimeQuery)->whereTime('last_login','>=','00:00:00')->whereTime('last_login','<','06:00:00')->count(),
        ];

        /* إحصاءات إضافية */
        $migrantQueryAll = \App\Models\MigrantProfile::query();
        if ($scopedUserIds !== null) $migrantQueryAll->whereIn('user_id', $scopedUserIds);

        $migrantStats = [
            'cultural_background' => (clone $migrantQueryAll)->select('cultural_background', \DB::raw('count(*) as total'))
                ->whereNotNull('cultural_background')->groupBy('cultural_background')->pluck('total','cultural_background'),
            'visa_category' => (clone $migrantQueryAll)->select('visa_category', \DB::raw('count(*) as total'))
                ->whereNotNull('visa_category')->groupBy('visa_category')->pluck('total','visa_category'),
            'business_stage' => (clone $migrantQueryAll)->select('business_stage', \DB::raw('count(*) as total'))
                ->whereNotNull('business_stage')->groupBy('business_stage')->pluck('total','business_stage'),
            'languages' => (clone $migrantQueryAll)->select('languages', \DB::raw('count(*) as total'))
                ->whereNotNull('languages')->groupBy('languages')->pluck('total','languages'),
            'arrival_year' => (clone $migrantQueryAll)->select('arrival_year', \DB::raw('count(*) as total'))
                ->whereNotNull('arrival_year')->groupBy('arrival_year')->orderBy('arrival_year')->pluck('total','arrival_year'),
        ];

        $migrantStats['employment_status'] = (clone $migrantQueryAll)
            ->select('employment_status', \DB::raw('count(*) as total'))
            ->whereNotNull('employment_status')
            ->groupBy('employment_status')
            ->pluck('total','employment_status');

        $migrantStats['education_level'] = (clone $migrantQueryAll)
            ->select('education_level', \DB::raw('count(*) as total'))
            ->whereNotNull('education_level')
            ->groupBy('education_level')
            ->pluck('total','education_level');

        $totalParticipants = (clone $migrantQueryAll)->count();

        /* Time in Country buckets */
        $years = (clone $migrantQueryAll)->whereNotNull('arrival_year')->pluck('arrival_year');
        $nowY = now()->year;
        $timeInCountry = ['0-1'=>0,'2-5'=>0,'5-10'=>0,'10+'=>0];
        foreach ($years as $y) {
            $d = max(0, $nowY - (int)$y);
            if     ($d <= 1)  $timeInCountry['0-1']++;
            elseif ($d <= 5)  $timeInCountry['2-5']++;
            elseif ($d <= 10) $timeInCountry['5-10']++;
            else              $timeInCountry['10+']++;
        }

        /* ===== تبويب Business & Progress (بدون أعمدة جديدة) ===== */
        // نفس سكوپ المستخدمين
     /* ===== تبويب Business & Progress ===== */
$migrantQueryScoped = \App\Models\MigrantProfile::query();
if ($scopedUserIds !== null) $migrantQueryScoped->whereIn('user_id', $scopedUserIds);

// IDs للربط مع EmploymentHistory
$profileIds = (clone $migrantQueryScoped)->pluck('id');

// 1) قبل الانضمام: من business_stage
$businessStageBefore = (clone $migrantQueryScoped)
    ->select('business_stage', \DB::raw('count(*) as total'))
    ->whereNotNull('business_stage')
    ->groupBy('business_stage')
    ->pluck('total','business_stage');

// 2) المكتمل: أعلى خطوة لكل مستخدم (تراكمي)
$stepModels = [
    'Business Idea'        => \App\Models\BusinessIdea::class,
    'Market Research'      => \App\Models\MarketResearch::class,
    'Marketing'            => \App\Models\ProductFeature::class,
    'MVP Development'      => \App\Models\SimpleSolution::class,
    'Sales Testing'        => \App\Models\TestingYourIdea::class,
    'Business Setup'       => \App\Models\ConversionRate::class,
    'Financial Planning'   => \App\Models\FinancialPlanner::class,
    'Website Requirements' => \App\Models\LegalStructure::class,
];

$usersPerStep = [];
$idx = 0;
foreach ($stepModels as $label => $model) {
    $idx++;
    if (!class_exists($model)) { $usersPerStep[$idx] = []; continue; }
    $q = $model::query();
    // نفلتر بالسكوپ فقط إذا كان بالجدول user_id
    if ($scopedUserIds !== null && \Illuminate\Support\Facades\Schema::hasColumn((new $model)->getTable(), 'user_id')) {
        $q->whereIn('user_id', $scopedUserIds);
    }
    $usersPerStep[$idx] = $q->distinct()->pluck('user_id')->all();
}
$maxStepForUser = [];
foreach ($usersPerStep as $stepIndex => $uids) {
    foreach ($uids as $u) {
        $maxStepForUser[$u] = max($maxStepForUser[$u] ?? 0, $stepIndex);
}
}
$totalUsersForProgress = max(1, count($maxStepForUser));
$labelsSteps = array_keys($stepModels);
$percentReached = [];
for ($j = 1; $j <= count($stepModels); $j++) {
    $cnt = 0; foreach ($maxStepForUser as $mx) if ($mx >= $j) $cnt++;
    $percentReached[] = round($cnt * 100 / $totalUsersForProgress);
}
$businessStageCompleted = ['labels' => $labelsSteps, 'percent' => $percentReached];

// 3) Personal Outcomes (بدائل تقريبية بدون أعمدة جديدة)
$personalOutcomes = [
    'language_improved'           => (clone $migrantQueryScoped)->whereNotNull('languages')->count(),
    'daily_employment_improved'   => (clone $migrantQueryScoped)->whereNotNull('employment_status')->count(),
    'business_clarity_increased'  => (clone $migrantQueryScoped)->whereNotNull('business_idea')->count(),
    'confidence_increased'        => (clone $migrantQueryScoped)->where('progress', '>=', 50)->count(), // عدّل العتبة إذا لزم
    'network_increased'           => (clone $migrantQueryScoped)->where('has_social_media', 1)->orWhereNotNull('social_links')->count(),
];

// 4) Employment/Education Outcomes
$employmentEducationOutcomes = [
    'started_working'      => \App\Models\EmploymentHistory::whereIn('profile_id', $profileIds)
                                  ->distinct('profile_id')->count('profile_id'),
    'started_studying'     => (clone $migrantQueryScoped)->where('is_studying', 1)->count(),
    'started_volunteering' => 0, // لا عمود؛ اتركه 0 أو غيّره إذا لديك بديل
];

// 5) Business Outcomes
$businessOutcomes = [
    'abn_registered'            => (clone $migrantQueryScoped)->where('has_abn', 1)->count(),
    'has_website'               => (clone $migrantQueryScoped)->where('has_website', 1)->count(),
    'domain_purchased'          => (clone $migrantQueryScoped)->whereNotNull('website_url')->count(),
    'business_name_registered'  => 0,
    'insurance'                 => 0,
    'started_selling'           => 0,
    'hired_employees'           => 0,
];

// إن كان لديك جدول businesses بهذه الأعمدة، استخدمه بدل البدائل:
if (\Illuminate\Support\Facades\Schema::hasTable('businesses')) {
    $bq = \App\Models\Business::query();
    if ($scopedUserIds !== null && \Illuminate\Support\Facades\Schema::hasColumn('businesses','user_id')) {
        $bq = $bq->whereIn('user_id', $scopedUserIds);
    }
    foreach (['abn_registered','domain_purchased','name_registered','insurance','started_selling','hired_employees'] as $col) {
        if (\Illuminate\Support\Facades\Schema::hasColumn('businesses', $col)) {
            $businessOutcomes[
              $col === 'name_registered' ? 'business_name_registered' : $col
            ] = (clone $bq)->where($col, 1)->count();
        }
    }
}

// تجميع بيانات Business & Progress في مصفوفة واحدة لسهولة الاستخدام في الـ View
$businessProgress = [
    'before_joining' => $businessStageBefore,
    'completed' => $businessStageCompleted,
    'personal' => $personalOutcomes,
    'emp_edu' => $employmentEducationOutcomes,
    'business' => $businessOutcomes
];
return view('ProgressAnalytics', compact(
    'sectionCompletion',
    'userActivity',
    'mostActiveSections',
    'ageGroups',
    'activityByTime',
    'migrantStats',
    'isFullAccess',
    'totalParticipants',
    'timeInCountry',
    // تبويب Business & Progress
    'businessStageBefore',
    'businessStageCompleted',
    'personalOutcomes',
    'employmentEducationOutcomes',
    'businessOutcomes',
    'businessProgress' // أضف هذا السطر
));
    }

}
