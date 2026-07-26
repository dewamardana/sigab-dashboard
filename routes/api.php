<?php

use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\SensorDataController;
use App\Http\Controllers\Api\NotificationLogController;
use Illuminate\Support\Facades\Route;

Route::middleware('api.key')->group(function () {
    Route::post('/sensor-data', [SensorDataController::class, 'store']);
    Route::get('/devices', [DeviceController::class, 'index']);
    Route::post('/notification-logs', [NotificationLogController::class, 'store']);
});
