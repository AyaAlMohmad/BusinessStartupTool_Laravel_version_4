<?php

use App\Http\Controllers\Admin\BusinessController;
use App\Http\Controllers\Admin\BusinessIdeaController;
use App\Http\Controllers\Admin\BusinessSetupController;
use App\Http\Controllers\Admin\ConversionRateController;
use App\Http\Controllers\Admin\FinancialPlannerController;
use App\Http\Controllers\Admin\MarketingNewController;
use App\Http\Controllers\Admin\TestingYourIdeaController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VideoController;
use App\Http\Controllers\Admin\MarketResearchController;
use App\Http\Controllers\Admin\RegionController;
use App\Http\Controllers\Admin\ResourceController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SimpleSolutionController;
use App\Http\Controllers\Admin\StoryController;
use App\Http\Controllers\Admin\WebsiteController;
use App\Http\Controllers\ProgressAnalyticsController;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/



Route::get('/', function () {
    return view('welcome');
});
Route::get('/index', function () {
    return url('/index.html');
});
// Route::get('/storage', function () {
//      $target = storage_path('app/public');
//     $link = public_path('storage');

//     if (!File::exists($link)) {
//         File::copyDirectory($target, $link);
//         return "Storage directory copied successfully!";
//     }

//     return "Storage link already exists!";
// });
Route::get('/das', function () {
    return view('dashboard');
})->middleware(['auth', 'verified']);





Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
// Route::prefix('admin/')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
//     Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
//     Route::resource('regions', RegionController::class);
//     Route::resource('resources', ResourceController::class);
//     Route::resource('videos', VideoController::class);
//     Route::get('/Progress', [ProgressAnalyticsController::class, 'index'])->name('ProgressAnalytics');
//     // Route::get('/', [UserController::class, 'index'])->name('index');
//     // Route::get('/{id}', [UserController::class, 'show'])->name('show');
//     Route::patch('/{id}/status', [UserController::class, 'changeStatus'])->name('changeStatus');
//     // Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
//     Route::resource('users', UserController::class);
//     Route::resource('roles',RoleController::class);
//     Route::resource('business-ideas', BusinessIdeaController::class);
//     Route::get('business/analysis', [BusinessIdeaController::class, 'analysis'])->name('ideas.analysis');
//     Route::resource('testing-your-idea', TestingYourIdeaController::class);
//     Route::get('testing/analysis', [TestingYourIdeaController::class, 'analysis'])->name('testing-your-idea.analysis');
//     Route::resource('marketing-new', MarketingNewController::class);
//     Route::get('marketing/analysis', [MarketingNewController::class, 'analysis'])->name('marketing-new.analysis');
//     Route::resource('market-research', MarketResearchController::class);
//     Route::get('market/analysis', [MarketResearchController::class, 'analysis'])->name('market-research.analysis');
//     Route::resource('start-simple', SimpleSolutionController::class);
//     Route::get('start/analysis', [SimpleSolutionController::class, 'analysis'])->name('start-simple.analysis');
//     Route::resource('landing-page', BusinessController::class);
//     Route::get('landing', [BusinessController::class, 'analysis'])->name('landing-page.analysis');
//     Route::resource('sales-strategies', ConversionRateController::class);
//     Route::get('sales/analysis', [ConversionRateController::class, 'analysis'])->name('sales-strategies.analysis');
//     Route::resource('business-setups', BusinessSetupController::class);
//     Route::get('business-setup/analysis', [BusinessSetupController::class, 'analysis'])->name('business-setup.analysis');
//     Route::resource('financial_planners', FinancialPlannerController::class);
//     Route::get('financial-planners/analysis', [FinancialPlannerController::class, 'analysis'])->name('financial_planners.analysis');
//     Route::resource('websites', WebsiteController::class);
//     Route::get('website/analysis', [WebsiteController::class, 'analysis'])->name('websites.analysis');

//     Route::resource('stories',StoryController::class);
//     Route::get('story/analysis', [StoryController::class, 'analysis'])->name('stories.analysis');
//     Route::get('admin/analytics/reset', function() {
//         $keys = \Illuminate\Support\Facades\Cache::get('pv:keys', []);
//         foreach ($keys as $k) \Illuminate\Support\Facades\Cache::forget($k);
//         \Illuminate\Support\Facades\Cache::forget('pv:users:all');
//         return 'Reset done';
//     })->middleware(['auth','admin'])->name('admin.analytics.reset');

// });
// في ملف routes/web.php
Route::prefix('admin/')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware('permission:view_dashboard');

    Route::resource('regions', RegionController::class)
        ->middleware('permission:manage_regions');

    Route::resource('resources', ResourceController::class)
        ->middleware('permission:manage_resources');

    Route::resource('videos', VideoController::class)
        ->middleware('permission:manage_videos');

    Route::get('/Progress', [ProgressAnalyticsController::class, 'index'])
        ->name('ProgressAnalytics')
        ->middleware('permission:view_progress_analytics');

    Route::patch('/{id}/status', [UserController::class, 'changeStatus'])
        ->name('changeStatus')
        ->middleware('permission:change_user_status');

    Route::resource('users', UserController::class)
        ->middleware('permission:manage_users');

    Route::resource('roles', RoleController::class)
        ->middleware('permission:manage_roles');

    // Business Ideas
    Route::resource('business-ideas', BusinessIdeaController::class)
        ->middleware('permission:manage_business_ideas');
    Route::get('business/analysis', [BusinessIdeaController::class, 'analysis'])
        ->name('ideas.analysis')
        ->middleware('permission:view_business_analysis');

    // Testing Your Idea
    Route::resource('testing-your-idea', TestingYourIdeaController::class)
        ->middleware('permission:manage_testing_ideas');
    Route::get('testing/analysis', [TestingYourIdeaController::class, 'analysis'])
        ->name('testing-your-idea.analysis')
        ->middleware('permission:view_testing_analysis');

    // Marketing
    Route::resource('marketing-new', MarketingNewController::class)
        ->middleware('permission:manage_marketing');
    Route::get('marketing/analysis', [MarketingNewController::class, 'analysis'])
        ->name('marketing-new.analysis')
        ->middleware('permission:view_marketing_analysis');

    // Market Research
    Route::resource('market-research', MarketResearchController::class)
        ->middleware('permission:manage_market_research');
    Route::get('market/analysis', [MarketResearchController::class, 'analysis'])
        ->name('market-research.analysis')
        ->middleware('permission:view_market_research_analysis');

    // Simple Solutions
    Route::resource('start-simple', SimpleSolutionController::class)
        ->middleware('permission:manage_simple_solutions');
    Route::get('start/analysis', [SimpleSolutionController::class, 'analysis'])
        ->name('start-simple.analysis')
        ->middleware('permission:view_simple_solutions_analysis');

    // Landing Page
    Route::resource('landing-page', BusinessController::class)
        ->middleware('permission:manage_landing_pages');
    Route::get('landing', [BusinessController::class, 'analysis'])
        ->name('landing-page.analysis')
        ->middleware('permission:view_landing_page_analysis');

    // Sales Strategies
    Route::resource('sales-strategies', ConversionRateController::class)
        ->middleware('permission:manage_sales_strategies');
    Route::get('sales/analysis', [ConversionRateController::class, 'analysis'])
        ->name('sales-strategies.analysis')
        ->middleware('permission:view_sales_analysis');

    // Business Setups
    Route::resource('business-setups', BusinessSetupController::class)
        ->middleware('permission:manage_business_setups');
    Route::get('business-setup/analysis', [BusinessSetupController::class, 'analysis'])
        ->name('business-setup.analysis')
        ->middleware('permission:view_business_setup_analysis');

    // Financial Planners
    Route::resource('financial_planners', FinancialPlannerController::class)
        ->middleware('permission:manage_financial_planners');
    Route::get('financial-planners/analysis', [FinancialPlannerController::class, 'analysis'])
        ->name('financial_planners.analysis')
        ->middleware('permission:view_financial_analysis');

    // Websites
    Route::resource('websites', WebsiteController::class)
        ->middleware('permission:manage_websites');
    Route::get('website/analysis', [WebsiteController::class, 'analysis'])
        ->name('websites.analysis')
        ->middleware('permission:view_website_analysis');

    // Stories
    Route::resource('stories', StoryController::class)
        ->middleware('permission:manage_stories');
    Route::get('story/analysis', [StoryController::class, 'analysis'])
        ->name('stories.analysis')
        ->middleware('permission:view_story_analysis');

    
});
require __DIR__ . '/auth.php';
