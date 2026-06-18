<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ReactController;
use App\Http\Controllers\Api\ReactLogController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\SectionController;
use App\Http\Controllers\Api\ExportController;
use App\Http\Controllers\Api\OptionSubmissionController;

// ── Public Routes ───────────────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me',      [AuthController::class, 'me']);
    });
});

Route::post('/companies/register', [CompanyController::class, 'register']);

// ── Protected Routes (All Authenticated Users) ──────────────────────────
Route::middleware(['auth:sanctum'])->group(function () {

    // ── React Types & Employee Reactions ─────────────────────────────────
    Route::prefix('reacts')->group(function () {
        Route::get('/types',    [ReactController::class, 'types']);
        Route::get('/my-today', [ReactController::class, 'myToday']);
        Route::post('/submit',  [ReactController::class, 'submit']);
    });

    // ── Options (All authenticated users — mobile app) ────────────────────
    Route::get('/options',         [OptionSubmissionController::class, 'index']);
    Route::post('/options/submit', [OptionSubmissionController::class, 'submit']);

    // ── Admin-Only Routes ────────────────────────────────────────────────
    Route::middleware(['role'])->group(function () {

        // ── Dashboard ────────────────────────────────────────────────────
        Route::prefix('dashboard')->group(function () {
            Route::get('/',              [DashboardController::class, 'index']);
            Route::get('/stats',         [DashboardController::class, 'stats']);
            Route::get('/pie-chart',     [DashboardController::class, 'pieChart']);
            Route::get('/bar-chart',     [DashboardController::class, 'barChart']);
            Route::get('/by-department', [DashboardController::class, 'byDepartment']);
            Route::get('/by-section',    [DashboardController::class, 'bySection']);
        });

        // ── Company Management ────────────────────────────────────────────
        Route::get('/companies/{company}',  [CompanyController::class, 'show']);
        Route::put('/companies/{company}',  [CompanyController::class, 'update']);

        // ── Employee Management ───────────────────────────────────────────
        Route::apiResource('employees', EmployeeController::class);
        Route::patch('/employees/{employee}/toggle-status', [EmployeeController::class, 'toggleStatus']);

        // ── Department & Section Management ───────────────────────────────
        Route::apiResource('departments', DepartmentController::class);
        Route::post('/sections', [SectionController::class, 'store']);

        // ── Reaction Logs ─────────────────────────────────────────────────
        Route::get('/logs',       [ReactLogController::class, 'index']);

        // ── Export ────────────────────────────────────────────────────────
        Route::get('/export', [ExportController::class, 'export']);
    });

    // ── Employee Reaction Submit (authenticated non-admin employees) ──────
    Route::post('/reactions', [ReactLogController::class, 'store']);
});