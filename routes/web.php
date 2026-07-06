<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\OptionController;

// ── Auth ─────────────────────────────────────────────────────────────────
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout',[AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ── Redirect root → dashboard ─────────────────────────────────────────────
Route::get('/', fn() => redirect()->route('dashboard'));

// ── Dashboard (auth required) ─────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // AJAX data endpoints
    Route::get('/dashboard/stats',         [DashboardController::class, 'stats'])->name('dashboard.stats');
    Route::get('/dashboard/pie-chart',     [DashboardController::class, 'pieChart'])->name('dashboard.pie');
    Route::get('/dashboard/bar-chart',     [DashboardController::class, 'barChart'])->name('dashboard.bar');
    Route::get('/dashboard/by-department', [DashboardController::class, 'byDepartment'])->name('dashboard.dept');
    Route::get('/dashboard/sections',      [DashboardController::class, 'sections'])->name('dashboard.sections');
    Route::get('/dashboard/logs',          [DashboardController::class, 'logs'])->name('dashboard.logs');

    // Management - Departments
    Route::get('/departments',                  [DashboardController::class, 'departmentsIndex'])->name('departments.index');
    Route::post('/departments',                 [DashboardController::class, 'storeDepartment'])->name('departments.store');
    Route::put('/departments/{department}',      [DashboardController::class, 'updateDepartment'])->name('departments.update');
    Route::delete('/departments/{department}',   [DashboardController::class, 'destroyDepartment'])->name('departments.destroy');
    Route::patch('/departments/{department}/toggle', [DashboardController::class, 'toggleDepartmentStatus'])->name('departments.toggle');

    // Management - Sections
    Route::get('/sections',                  [DashboardController::class, 'sectionsIndex'])->name('sections.index');
    Route::post('/sections',                 [DashboardController::class, 'storeSection'])->name('sections.store');
    Route::put('/sections/{section}',        [DashboardController::class, 'updateSection'])->name('sections.update');
    Route::delete('/sections/{section}',     [DashboardController::class, 'destroySection'])->name('sections.destroy');
    Route::patch('/sections/{section}/toggle', [DashboardController::class, 'toggleSectionStatus'])->name('sections.toggle');

    // Export
    Route::get('/export', [DashboardController::class, 'export'])->name('export');

    // ── Options Management ────────────────────────────────────────────────
    Route::get('/options',                  [OptionController::class, 'index'])->name('options.index');
    Route::post('/options',                 [OptionController::class, 'store'])->name('options.store');
    Route::put('/options/{option}',         [OptionController::class, 'update'])->name('options.update');
    Route::delete('/options/{option}',      [OptionController::class, 'destroy'])->name('options.destroy');
    Route::patch('/options/{option}/toggle',[OptionController::class, 'toggleStatus'])->name('options.toggle');

    // ── Options Dashboard Stats ──────────────────────────────────────────
    Route::get('/dashboard/options-stats', [DashboardController::class, 'optionStats'])->name('options.dashboard.stats');
});
