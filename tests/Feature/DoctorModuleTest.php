<?php

use App\Livewire\Doctor\DoctorIndex;
use App\Livewire\Doctor\DoctorSchedule as DoctorScheduleComponent;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

// ─── Helpers ────────────────────────────────────────────────────────────────

function adminUser(): User
{
    return User::factory()->create(['role' => 'admin', 'is_active' => 1]);
}

function nonAdminUser(): User
{
    return User::factory()->create(['role' => 'cashier', 'is_active' => 1]);
}

// ─── Route access ────────────────────────────────────────────────────────────

test('admin can visit /doctors', function () {
    actingAs(adminUser())
        ->get('/doctors')
        ->assertOk();
});

test('unauthenticated user is redirected from /doctors', function () {
    $this->get('/doctors')->assertRedirect('/login');
});

test('non-admin role gets 403 on DoctorIndex', function () {
    actingAs(nonAdminUser());

    $this->get('/doctors')->assertForbidden();
});

// ─── DoctorIndex component ───────────────────────────────────────────────────

test('DoctorIndex lists doctors', function () {
    actingAs(adminUser());

    Doctor::factory()->count(3)->create();

    Livewire::test(DoctorIndex::class)
        ->assertSee(Doctor::first()->name);
});

test('DoctorIndex search filters by name', function () {
    actingAs(adminUser());

    Doctor::factory()->create(['name' => 'Dr. Unik Banget', 'specialization' => 'Umum']);
    Doctor::factory()->create(['name' => 'Dr. Biasa', 'specialization' => 'Gigi']);

    Livewire::test(DoctorIndex::class)
        ->set('search', 'Unik')
        ->assertSee('Dr. Unik Banget')
        ->assertDontSee('Dr. Biasa');
});

test('DoctorIndex toggleActive flips is_active', function () {
    actingAs(adminUser());

    $doctor = Doctor::factory()->create(['is_active' => true]);

    Livewire::test(DoctorIndex::class)
        ->call('toggleActive', $doctor->id);

    expect($doctor->fresh()->is_active)->toBeFalse();
});

test('DoctorIndex delete removes the doctor', function () {
    actingAs(adminUser());

    $doctor = Doctor::factory()->create();

    Livewire::test(DoctorIndex::class)
        ->call('delete', $doctor->id);

    $this->assertModelMissing($doctor);
});

test('DoctorIndex closes form on doctor-saved event', function () {
    actingAs(adminUser());

    Livewire::test(DoctorIndex::class)
        ->call('openCreate')
        ->assertSet('showForm', true)
        ->dispatch('doctor-saved')
        ->assertSet('showForm', false);
});

// ─── DoctorSchedule component ────────────────────────────────────────────────

test('admin can visit doctor schedule page', function () {
    actingAs(adminUser());

    $doctor = Doctor::factory()->create();

    $this->get(route('doctors.schedules', $doctor->id))->assertOk();
});

test('DoctorSchedule save adds a schedule', function () {
    actingAs(adminUser());

    $doctor = Doctor::factory()->create();

    Livewire::test(DoctorScheduleComponent::class, ['id' => $doctor->id])
        ->set('day_of_week', 'Senin')
        ->set('start_time', '08:00')
        ->set('end_time', '12:00')
        ->set('slot_duration_minutes', 30)
        ->call('save');

    expect(DoctorSchedule::where('doctor_id', $doctor->id)->where('day_of_week', 'Senin')->exists())->toBeTrue();
});

test('DoctorSchedule validation rejects duplicate day', function () {
    actingAs(adminUser());

    $doctor = Doctor::factory()->create();
    DoctorSchedule::create([
        'doctor_id' => $doctor->id,
        'day_of_week' => 'Senin',
        'start_time' => '08:00:00',
        'end_time' => '12:00:00',
        'slot_duration_minutes' => 30,
        'is_active' => 1,
    ]);

    Livewire::test(DoctorScheduleComponent::class, ['id' => $doctor->id])
        ->set('day_of_week', 'Senin')
        ->set('start_time', '09:00')
        ->set('end_time', '13:00')
        ->set('slot_duration_minutes', 30)
        ->call('save')
        ->assertHasErrors(['day_of_week']);
});

test('DoctorSchedule validation rejects end_time before start_time', function () {
    actingAs(adminUser());

    $doctor = Doctor::factory()->create();

    Livewire::test(DoctorScheduleComponent::class, ['id' => $doctor->id])
        ->set('day_of_week', 'Selasa')
        ->set('start_time', '12:00')
        ->set('end_time', '08:00')
        ->set('slot_duration_minutes', 30)
        ->call('save')
        ->assertHasErrors(['end_time']);
});

test('DoctorSchedule deleteSchedule removes the row', function () {
    actingAs(adminUser());

    $doctor = Doctor::factory()->create();
    $schedule = DoctorSchedule::create([
        'doctor_id' => $doctor->id,
        'day_of_week' => 'Rabu',
        'start_time' => '08:00:00',
        'end_time' => '12:00:00',
        'slot_duration_minutes' => 30,
        'is_active' => 1,
    ]);

    Livewire::test(DoctorScheduleComponent::class, ['id' => $doctor->id])
        ->call('deleteSchedule', $schedule->id);

    $this->assertModelMissing($schedule);
});
