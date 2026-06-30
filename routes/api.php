<?php

use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\SpecializationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public API Routes — api/* prefix, no auth required
|--------------------------------------------------------------------------
| These routes are for the public-facing React frontend.
| Livewire / web routes are completely separate.
*/

// Specializations (categories)
Route::middleware('throttle:60,1')->group(function () {
    Route::get('/specializations', [SpecializationController::class, 'index'])
        ->name('api.specializations.index');

    // Doctors
    Route::get('/doctors', [DoctorController::class, 'index'])
        ->name('api.doctors.index');

    Route::get('/doctors/{slug}', [DoctorController::class, 'show'])
        ->name('api.doctors.show')
        ->where('slug', '[a-z0-9\-]+');

    Route::get('/doctors/{id}/slots', [DoctorController::class, 'availableSlots'])
        ->name('api.doctors.slots')
        ->where('id', '[0-9]+');
});

// Appointments — tighter rate limit (5 per minute)
Route::middleware('throttle:5,1')->group(function () {
    Route::post('/appointments', [AppointmentController::class, 'store'])
        ->name('api.appointments.store');
});
