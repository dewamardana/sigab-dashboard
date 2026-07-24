<?php

use App\Http\Controllers\DeviceController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProfileController;
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
    Route::get('/dashboard-test', function () {
        return 'Halo Superadmin, akses berhasil!';
    });

    Route::resource('locations', LocationController::class)
        ->except(['create', 'edit', 'show']);

    Route::resource('users', UserController::class)
        ->except(['create', 'edit', 'show']);
});


Route::middleware(['auth', 'role_or_permission:superadmin|admin_lokasi'])->group(function () {
    Route::resource('devices', DeviceController::class)
        ->except(['create', 'edit', 'show']);
});

require __DIR__ . '/auth.php';
