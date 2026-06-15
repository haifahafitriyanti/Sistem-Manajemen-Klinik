<?php

use App\Livewire\Doctor\DoctorIndex;
use App\Livewire\Doctor\DoctorSchedule;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::get('/doctors', DoctorIndex::class)->name('doctors.index');
    Route::get('/doctors/{id}/schedules', DoctorSchedule::class)->name('doctors.schedules');
});

require __DIR__.'/settings.php';
