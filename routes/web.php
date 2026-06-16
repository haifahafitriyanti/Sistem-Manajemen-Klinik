<?php

use App\Livewire\Appointment\AppointmentIndex;
use App\Livewire\Doctor\DoctorIndex;
use App\Livewire\Doctor\DoctorSchedule;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::get('/doctors', DoctorIndex::class)->name('doctors.index');
    Route::get('/doctors/{id}/schedules', DoctorSchedule::class)->name('doctors.schedules');

    Route::get('/appointments', AppointmentIndex::class)->name('appointments.index');

    // Stub for the EMR examine page — will be replaced in the next module
    Route::get('/appointments/{id}/examine', fn () => abort(404))->name('appointments.examine');
});

require __DIR__.'/settings.php';
