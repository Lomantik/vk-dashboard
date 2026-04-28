<?php

use App\Http\Controllers\Api\AnalysisController;
use App\Http\Controllers\Api\HealthController;
use Illuminate\Support\Facades\Route;

Route::post('/analyze', [AnalysisController::class, 'analyze']);
Route::get('/report/{groupId}', [AnalysisController::class, 'report']);
Route::get('/health', [HealthController::class, 'index']);
