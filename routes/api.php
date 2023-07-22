<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IssuesController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\TrackerController;
use App\Http\Controllers\IssuePriorityController;

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


Route::get('/issues', [IssuesController::class, 'index']);
Route::post('/issues/save', [IssuesController::class, 'save']);
Route::post('/issues/delete', [IssuesController::class, 'destroy']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/projects', [ProjectController::class, 'index']);
Route::get('/apiTest', [CategoryController::class, 'redmineProjectCategories']);
Route::get('/statuses', [StatusController::class, 'index']);
Route::get('/trackers', [TrackerController::class, 'index']);
Route::get('/project/{id}', [ProjectController::class, 'show']);
Route::get('/issue-priorities', [IssuePriorityController::class, 'index']);