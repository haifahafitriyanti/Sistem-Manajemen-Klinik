<?php

use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\DoctorController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:60,1')->group(function () {
    Route::get('/doctors', [DoctorController::class, 'index']);
    Route::get('/doctors/{id}/slots', [DoctorController::class, 'availableSlots']);
    Route::post('/appointments', [AppointmentController::class, 'store']);
});
