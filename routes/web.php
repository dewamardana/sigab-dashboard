<?php

use App\Http\Controllers\AdminMonitoringController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\NotificationLogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PublicController;

Route::get('/', [PublicController::class, 'index'])->name('public.index');
Route::get('/monitoring/{location}', [PublicController::class, 'show'])->name('public.show');
Route::get('/monitoring/{location}/{device}', [PublicController::class, 'device'])->name('public.device');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:superadmin'])->group(function () {
    Route::resource('locations', LocationController::class)
        ->except(['create', 'edit', 'show']);

    Route::resource('users', UserController::class)
        ->except(['create', 'edit', 'show']);
});


Route::middleware(['auth', 'role_or_permission:superadmin|admin_lokasi'])->group(function () {
    Route::resource('devices', DeviceController::class)
        ->except(['create', 'edit', 'show']);

    Route::get('/dashboard/monitoring', [AdminMonitoringController::class, 'index'])
        ->name('admin.monitoring');
    Route::get('/dashboard/monitoring/{location}/{device}', [AdminMonitoringController::class, 'device'])
        ->name('admin.monitoring.device');

    Route::get('/notifications', [NotificationLogController::class, 'index'])
        ->name('notifications.index');

    Route::get('/reports', [ReportController::class, 'index'])
        ->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])
        ->name('reports.export');
});

require __DIR__ . '/auth.php';
