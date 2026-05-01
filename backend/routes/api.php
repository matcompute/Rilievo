<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContractorController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\IssueController;
use App\Http\Controllers\Api\LookupsController;
use App\Http\Controllers\Api\PermitController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\SiteDiaryController;
use Illuminate\Support\Facades\Route;

Route::get('/health', fn () => ['status' => 'ok']);

Route::post('/login', [AuthController::class, 'login']);

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::prefix('auth')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });

    Route::get('/dashboard', [DashboardController::class, 'summary']);
    Route::get('/lookups', [LookupsController::class, 'index']);

    Route::get('/projects', [ProjectController::class, 'index']);
    Route::get('/projects/{project}', [ProjectController::class, 'show']);
    Route::post('/projects', [ProjectController::class, 'store']);
    Route::patch('/projects/{project}', [ProjectController::class, 'update']);

    Route::get('/permits', [PermitController::class, 'index']);
    Route::post('/permits', [PermitController::class, 'store']);
    Route::patch('/permits/{permit}', [PermitController::class, 'update']);

    Route::get('/contractors', [ContractorController::class, 'index']);
    Route::post('/contractors', [ContractorController::class, 'store']);
    Route::patch('/contractors/{contractor}', [ContractorController::class, 'update']);

    Route::get('/site-diary', [SiteDiaryController::class, 'index']);
    Route::post('/site-diary', [SiteDiaryController::class, 'store']);

    Route::get('/issues', [IssueController::class, 'index']);
    Route::post('/issues', [IssueController::class, 'store']);
    Route::patch('/issues/{issue}', [IssueController::class, 'update']);
});
