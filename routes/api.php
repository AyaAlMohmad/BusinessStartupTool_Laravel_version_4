<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BusinessController;
use App\Http\Controllers\API\FinancialPlanningController;
use App\Http\Controllers\API\BusinessIdeaController;
use App\Http\Controllers\API\BusinessSetupController;
use App\Http\Controllers\API\ConversionRateController;
use App\Http\Controllers\API\DownloadController;
use App\Http\Controllers\API\FinancialPlannerController;
use App\Http\Controllers\API\ImageController;
use App\Http\Controllers\API\LaunchPreparationController;
use App\Http\Controllers\API\LegalStructureController;
use App\Http\Controllers\API\MarketingChannelController;
use App\Http\Controllers\API\MarketingController;
use App\Http\Controllers\API\MarketingNewController;
use App\Http\Controllers\API\MarketResearchController;
use App\Http\Controllers\API\MigrantProfileController;
use App\Http\Controllers\API\MVPDevelopmentController;
use App\Http\Controllers\API\SalesStrategyController;
use App\Http\Controllers\API\VideoController;
use App\Http\Controllers\API\PasswordResetController;
use App\Http\Controllers\API\PersonalUpdateController;
use App\Http\Controllers\API\RegionController;
use App\Http\Controllers\API\ResourceController;
use App\Http\Controllers\API\SimpleSolutionController;
use App\Http\Controllers\API\StoryController;
use App\Http\Controllers\API\TestingYourIdeaController;
use App\Http\Controllers\API\WebsiteController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/password/email', [PasswordResetController::class, 'sendResetLinkEmail']);
Route::post('/password/reset/{token}', [PasswordResetController::class, 'resetPassword']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('businesses', BusinessController::class);
    Route::get('businesses/{business}/logs', [BusinessController::class, 'showLogs']);

    // Business Idea Routes
    Route::prefix('business-ideas')->group(function () {
        Route::get('/', [BusinessIdeaController::class, 'index']);
        Route::get('/{id}', [BusinessIdeaController::class, 'show']);
        Route::put('/{id}', [BusinessIdeaController::class, 'update']);
        Route::post('/', [BusinessIdeaController::class, 'store']);
        Route::patch('/progress', [BusinessIdeaController::class, 'updateProgress']);
    });

    // Testing Ideas Routes - التصحيح هنا
    Route::prefix('testing-ideas')->group(function () {
        Route::get('/', [TestingYourIdeaController::class, 'index']);
        Route::get('/{id}', [TestingYourIdeaController::class, 'show']);
        Route::post('/', [TestingYourIdeaController::class, 'store']);
        Route::put('/{id}', [TestingYourIdeaController::class, 'update']);
        Route::delete('/{id}', [TestingYourIdeaController::class, 'destroy']);
        Route::patch('/progress', [TestingYourIdeaController::class, 'updateProgress']);
    });

    // Market Research Routes
    Route::prefix('market-researches')->group(function () {
        Route::get('/', [MarketResearchController::class, 'index']);
        Route::get('/{id}', [MarketResearchController::class, 'show']);
        Route::post('/', [MarketResearchController::class, 'store']);
        Route::put('/{id}', [MarketResearchController::class, 'update']);
        Route::delete('/{id}', [MarketResearchController::class, 'destroy']);
        Route::patch('/progress', [MarketResearchController::class, 'updateProgress']);
    });

    // Simple Solutions Routes
    Route::prefix('simple-solutions')->group(function () {
        Route::get('/', [SimpleSolutionController::class, 'index']);
        Route::post('/', [SimpleSolutionController::class, 'store']);
        Route::get('/{id}', [SimpleSolutionController::class, 'show']);
        Route::put('/{id}', [SimpleSolutionController::class, 'update']);
        Route::delete('/{id}', [SimpleSolutionController::class, 'destroy']);
        Route::patch('/progress', [SimpleSolutionController::class, 'updateProgress']);
    });

    // Marketing New Routes
    Route::prefix('marketing-new')->group(function () {
        Route::get('/', [MarketingNewController::class, 'index']);
        Route::get('/{id}', [MarketingNewController::class, 'show']);
        Route::post('/', [MarketingNewController::class, 'store']);
        Route::put('/{id}', [MarketingNewController::class, 'update']);
        Route::delete('/{id}', [MarketingNewController::class, 'destroy']);
        Route::patch('/progress', [MarketingNewController::class, 'updateProgress']);
    });

    // Conversion Rate Routes
    Route::prefix('sales-conversion-notes')->group(function () {
        Route::get('/', [ConversionRateController::class, 'index']);
        Route::get('/{id}', [ConversionRateController::class, 'show']);
        Route::post('/', [ConversionRateController::class, 'store']);
        Route::put('/{id}', [ConversionRateController::class, 'update']);
        Route::delete('/{id}', [ConversionRateController::class, 'destroy']);
        Route::patch('/progress', [ConversionRateController::class, 'updateProgress']);
    });

    // بقية الـ routes...
    Route::get('/regions', [RegionController::class, 'index']);
    Route::get('/resources', [ResourceController::class, 'index']);
    Route::get('/resources/region/{region_id}', [ResourceController::class, 'byRegion']);
    Route::get('/resources/global', [ResourceController::class, 'global']);
    Route::get('/resources/private', [ResourceController::class, 'private'])->middleware('auth:sanctum');
    Route::get('/resources/local/{region_id}', [ResourceController::class, 'local']);
    Route::get('/resources/user/{user_id}', [ResourceController::class, 'forUser']);
    Route::get('/resources/unassigned', [ResourceController::class, 'unassigned']);
    Route::get('/resources/search', [ResourceController::class, 'search']);
    Route::apiResource('images', ImageController::class);
    Route::post('edit/images/{id}', [ImageController::class, 'update']);
    Route::get('users/{userId}/images', [ImageController::class, 'getUserImages']);
    Route::prefix('migrant-profiles')->group(function () {
        Route::get('/', [MigrantProfileController::class, 'index']);
        Route::get('/{id}', [MigrantProfileController::class, 'show']);
        Route::put('/{id}', [MigrantProfileController::class, 'update']);
        Route::post('/', [MigrantProfileController::class, 'store']);
        Route::delete('/{id}', [MigrantProfileController::class, 'destroy']);
    });

    Route::prefix('stories')->group(function () {
        Route::get('/', [StoryController::class, 'index']);
        Route::post('/', [StoryController::class, 'store']);
        Route::get('/{id}', [StoryController::class, 'show']);
        Route::post('/{id}', [StoryController::class, 'update']);
        Route::delete('/{id}', [StoryController::class, 'destroy']);
    });

    Route::get('/download-business-data', [DownloadController::class, 'downloadBusinessData']);

    Route::get('/videos/search', [VideoController::class, 'searchByTitle']);
    Route::get('/videos', [VideoController::class, 'index']);
    Route::get('/videos/{id}', [VideoController::class, 'show']);

    // Business Setup Routes
    Route::prefix('business-setups')->group(function () {
        Route::get('/', [LegalStructureController::class, 'index']);
        Route::post('/', [LegalStructureController::class, 'store']);
        Route::get('/{id}', [LegalStructureController::class, 'show']);
        Route::put('/{id}', [LegalStructureController::class, 'update']);
        Route::delete('/{id}', [LegalStructureController::class, 'destroy']);
        Route::patch('/progress', [LegalStructureController::class, 'updateProgress']);
    });

    Route::prefix('financial-planner')->group(function () {
        Route::get('/', [FinancialPlannerController::class, 'index']);
        Route::get('/{id}', [FinancialPlannerController::class, 'show']);
        Route::put('/{id}', [FinancialPlannerController::class, 'update']);
        Route::post('/', [FinancialPlannerController::class, 'store']);
        Route::patch('/progress', [FinancialPlannerController::class, 'updateProgress']);
    });

    Route::prefix('websites')->group(function () {
        Route::get('/', [WebsiteController::class, 'index']);
        Route::get('/{id}', [WebsiteController::class, 'show']);
        Route::post('/', [WebsiteController::class, 'store']);
        Route::put('/{id}', [WebsiteController::class, 'update']);
        Route::patch('/progress', [WebsiteController::class, 'updateProgress']);
    });
    Route::prefix('personal-updates')->group(function () {
        Route::get('/', [PersonalUpdateController::class, 'index']);
        Route::get('/{id}', [PersonalUpdateController::class, 'show']);
        Route::post('/', [PersonalUpdateController::class, 'store']);
        Route::put('/{id}', [PersonalUpdateController::class, 'update']);
        Route::delete('/{id}', [PersonalUpdateController::class, 'destroy']);
    });
});
