<?php

use App\Livewire\Appointment\AppointmentIndex;
use App\Livewire\Doctor\DoctorIndex;
use App\Livewire\Doctor\DoctorSchedule;
use App\Livewire\Medical\MedicalRecordForm;
use App\Livewire\Medical\PatientHistory;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::get('/doctors', DoctorIndex::class)->name('doctors.index');
    Route::get('/doctors/{id}/schedules', DoctorSchedule::class)->name('doctors.schedules');

    Route::get('/appointments', AppointmentIndex::class)->name('appointments.index');
    Route::get('/appointments/{appointmentId}/examine', MedicalRecordForm::class)->name('appointments.examine');

    Route::get('/patients/{patientId}/history', PatientHistory::class)->name('patients.history');
});

require __DIR__.'/settings.php';
