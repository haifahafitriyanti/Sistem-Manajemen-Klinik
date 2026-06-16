<?php

use App\Livewire\Appointment\AppointmentForm;
use App\Livewire\Appointment\AppointmentIndex;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

// ─── Helpers ────────────────────────────────────────────────────────────────

function makeAdmin(): User
{
    return User::factory()->create(['role' => 'admin', 'is_active' => 1]);
}

function makeReceptionist(): User
{
    return User::factory()->create(['role' => 'receptionist', 'is_active' => 1]);
}

function makeCashier(): User
{
    return User::factory()->create(['role' => 'cashier', 'is_active' => 1]);
}

function doctorWithSchedule(): Doctor
{
    $doctor = Doctor::factory()->create(['is_active' => true]);

    // Monday schedule
    DoctorSchedule::create([
        'doctor_id' => $doctor->id,
        'day_of_week' => 'Senin',
        'start_time' => '08:00:00',
        'end_time' => '10:00:00',
        'slot_duration_minutes' => 30,
        'is_active' => 1,
    ]);

    return $doctor;
}

// ─── Route access ─────────────────────────────────────────────────────────────

test('admin can visit /appointments', function () {
    actingAs(makeAdmin())
        ->get('/appointments')
        ->assertOk();
});

test('receptionist can visit /appointments', function () {
    actingAs(makeReceptionist())
        ->get('/appointments')
        ->assertOk();
});

test('cashier gets 403 on /appointments', function () {
    actingAs(makeCashier())
        ->get('/appointments')
        ->assertForbidden();
});

test('unauthenticated user is redirected from /appointments', function () {
    $this->get('/appointments')->assertRedirect('/login');
});

// ─── AppointmentIndex ─────────────────────────────────────────────────────────

test('AppointmentIndex shows today\'s appointments', function () {
    actingAs(makeAdmin());

    $apt = Appointment::factory()->create([
        'date' => today()->toDateString(),
        'status' => 'waiting',
    ]);

    Livewire::test(AppointmentIndex::class)
        ->assertSee($apt->patient_name);
});

test('AppointmentIndex does not show tomorrow\'s appointments', function () {
    actingAs(makeAdmin());

    $apt = Appointment::factory()->create([
        'date' => today()->addDay()->toDateString(),
        'status' => 'waiting',
    ]);

    Livewire::test(AppointmentIndex::class)
        ->assertDontSee($apt->patient_name);
});

test('AppointmentIndex search filters by patient name', function () {
    actingAs(makeAdmin());

    Appointment::factory()->create(['patient_name' => 'Budi Unik', 'date' => today()]);
    Appointment::factory()->create(['patient_name' => 'Siti Biasa', 'date' => today()]);

    Livewire::test(AppointmentIndex::class)
        ->set('search', 'Unik')
        ->assertSee('Budi Unik')
        ->assertDontSee('Siti Biasa');
});

test('AppointmentIndex startExamination changes status to in_progress', function () {
    actingAs(makeAdmin());

    $apt = Appointment::factory()->waiting()->create(['date' => today()]);

    Livewire::test(AppointmentIndex::class)
        ->call('startExamination', $apt->id);

    expect($apt->fresh()->status)->toBe('in_progress');
});

test('AppointmentIndex confirmCancel cancels appointment with reason', function () {
    actingAs(makeAdmin());

    $apt = Appointment::factory()->waiting()->create(['date' => today()]);

    Livewire::test(AppointmentIndex::class)
        ->call('initCancel', $apt->id)
        ->set('cancellationReason', 'Pasien tidak hadir')
        ->call('confirmCancel', $apt->id);

    $apt->refresh();
    expect($apt->status)->toBe('cancelled');
    expect($apt->cancellation_reason)->toBe('Pasien tidak hadir');
});

// ─── AppointmentForm ──────────────────────────────────────────────────────────

test('AppointmentForm loads available slots when doctor and date are set', function () {
    actingAs(makeAdmin());

    $doctor = doctorWithSchedule();
    $monday = today()->isMonday() ? today() : today()->next('Monday');

    Livewire::test(AppointmentForm::class)
        ->set('doctor_id', $doctor->id)
        ->set('date', $monday->toDateString())
        ->assertSet('noScheduleMessage', '');
});

test('AppointmentForm shows no-schedule message for inactive day', function () {
    actingAs(makeAdmin());

    $doctor = Doctor::factory()->create(['is_active' => true]);
    // No schedule added — any date will have no schedule

    $saturday = now()->startOfWeek()->addDays(5)->toDateString();

    Livewire::test(AppointmentForm::class)
        ->set('doctor_id', $doctor->id)
        ->set('date', $saturday)
        ->assertSet('availableSlots', []);
});

test('AppointmentForm saves appointment and dispatches event', function () {
    actingAs(makeAdmin());

    $doctor = doctorWithSchedule();

    // Find the next Monday (today if today is Monday, otherwise next Monday)
    $monday = today()->isMonday() ? today() : today()->next('Monday');
    $mondayStr = $monday->toDateString();

    Livewire::test(AppointmentForm::class)
        ->set('doctor_id', $doctor->id)
        ->set('date', $mondayStr)
        ->call('loadAvailableSlots')
        ->set('time_slot', '08:00')
        ->set('patient_name', 'Pasien Test')
        ->set('phone', '08111111111')
        ->call('save')
        ->assertDispatched('appointment-saved');

    expect(Appointment::where('patient_name', 'Pasien Test')->exists())->toBeTrue();
});

test('AppointmentForm validation rejects past date', function () {
    actingAs(makeAdmin());

    $doctor = Doctor::factory()->create(['is_active' => true]);

    Livewire::test(AppointmentForm::class)
        ->set('doctor_id', $doctor->id)
        ->set('date', today()->subDay()->toDateString())
        ->set('time_slot', '08:00')
        ->set('patient_name', 'Test')
        ->set('phone', '081234567890')
        ->call('save')
        ->assertHasErrors(['date']);
});

test('AppointmentForm validation requires patient name and phone', function () {
    actingAs(makeAdmin());

    $doctor = Doctor::factory()->create(['is_active' => true]);

    Livewire::test(AppointmentForm::class)
        ->set('doctor_id', $doctor->id)
        ->set('date', today()->toDateString())
        ->set('time_slot', '08:00')
        ->set('patient_name', '')
        ->set('phone', '')
        ->call('save')
        ->assertHasErrors(['patient_name', 'phone']);
});

// ─── Appointment model ────────────────────────────────────────────────────────

test('generateQueueNumber returns correct next number', function () {
    $doctor = Doctor::factory()->create();

    // 2 non-cancelled appointments today
    Appointment::factory()->count(2)->create([
        'doctor_id' => $doctor->id,
        'date' => today(),
        'status' => 'waiting',
    ]);

    // 1 cancelled — should NOT count
    Appointment::factory()->cancelled()->create([
        'doctor_id' => $doctor->id,
        'date' => today(),
    ]);

    $newAppointment = new Appointment([
        'doctor_id' => $doctor->id,
        'date' => today(),
    ]);

    expect($newAppointment->generateQueueNumber())->toBe(3);
});

test('Appointment scopeToday only returns today', function () {
    Appointment::factory()->create(['date' => today()]);
    Appointment::factory()->create(['date' => today()->addDay()]);

    expect(Appointment::today()->count())->toBe(1);
});

test('Appointment scopeByStatus filters correctly', function () {
    Appointment::factory()->waiting()->create(['date' => today()]);
    Appointment::factory()->done()->create(['date' => today()]);

    expect(Appointment::byStatus('waiting')->count())->toBe(1);
});
