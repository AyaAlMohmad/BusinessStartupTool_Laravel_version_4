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



Route::prefix('testing-ideas')->group(function () {
    Route::get('/', [TestingYourIdeaController::class, 'index']);
    Route::get('/{id}', [TestingYourIdeaController::class, 'show']);
    Route::post('/', [TestingYourIdeaController::class, 'store']);
    Route::put('/{id}', [TestingYourIdeaController::class, 'update']);
    Route::delete('/{id}', [TestingYourIdeaController::class, 'destroy']);
    Route::patch('/progress', [TestingYourIdeaController::class, 'updateProgress']);
});
    Route::apiResource('market-researches', MarketResearchController::class);
    Route::patch('/market-researches/progress', [MarketResearchController::class, 'updateProgress']);

    Route::prefix('simple-solutions')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [SimpleSolutionController::class, 'index']);
        Route::post('/', [SimpleSolutionController::class, 'store']);
        Route::get('/{id}', [SimpleSolutionController::class, 'show']);
        Route::put('/{id}', [SimpleSolutionController::class, 'update']);
        Route::delete('/{id}', [SimpleSolutionController::class, 'destroy']);
        Route::patch('/progress', [SimpleSolutionController::class, 'updateProgress']);
    });

    Route::apiResource('marketing-new', MarketingNewController::class);
    Route::patch('/marketing-new/progress', [MarketingNewController::class, 'updateProgress']);


    Route::apiResource('sales-conversion-notes', ConversionRateController::class);
    Route::patch('/sales-conversion-notes/progress', [ConversionRateController::class, 'updateProgress']);




    Route::get('/regions', [RegionController::class, 'index']);
    Route::get('/resources', [ResourceController::class, 'index']);
    Route::get('/resources/region/{region_id}', [ResourceController::class, 'byRegion']);
    Route::prefix('migrant-profiles')->group(function () {
        Route::get('/', [MigrantProfileController::class, 'index']);
        Route::get('/{id}', [MigrantProfileController::class, 'show']);
        Route::put('/{id}', [MigrantProfileController::class, 'update']);
        Route::post('/', [MigrantProfileController::class, 'store']);
        Route::delete('/{id}', [MigrantProfileController::class, 'destroy']);
    });



    Route::prefix('stories')->middleware('auth:sanctum')->group(function () {
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



    Route::get('/financial-planner', [FinancialPlannerController::class, 'index']); // GET all
    Route::get('/financial-planner/{id}', [FinancialPlannerController::class, 'show']); // GET by ID
    Route::put('/financial-planner/{id}', [FinancialPlannerController::class, 'update']);
    Route::post('/financial-planner', [FinancialPlannerController::class, 'store']);
    Route::post('/financial-planner/progress', [FinancialPlannerController::class, 'updateProgress']);





    Route::get('/websites', [WebsiteController::class, 'index']);
    Route::get('/websites/{id}', [WebsiteController::class, 'show']);
    Route::post('/websites', [WebsiteController::class, 'store']);
    Route::put('/websites/{id}', [WebsiteController::class, 'update']);
    Route::patch('/websites/progress', [WebsiteController::class, 'updateProgress']);



   });
