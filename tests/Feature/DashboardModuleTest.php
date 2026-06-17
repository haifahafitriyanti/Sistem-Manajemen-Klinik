<?php

use App\Livewire\Dashboard;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

// ─── Helpers ─────────────────────────────────────────────────────────────────

function dashAdmin(): User
{
    return User::factory()->create(['role' => 'admin', 'is_active' => 1]);
}

function dashDoctor(): User
{
    return User::factory()->create(['role' => 'doctor', 'is_active' => 1]);
}

function dashReceptionist(): User
{
    return User::factory()->create(['role' => 'receptionist', 'is_active' => 1]);
}

// ─── Route access ─────────────────────────────────────────────────────────────

test('authenticated users can visit /dashboard', function () {
    actingAs(dashAdmin())->get('/dashboard')->assertOk();
});

test('unauthenticated users are redirected from /dashboard', function () {
    $this->get('/dashboard')->assertRedirect('/login');
});

// ─── Row 1: Today's appointment counts ───────────────────────────────────────

test('Dashboard shows correct total for today', function () {
    actingAs(dashAdmin());

    Appointment::factory()->count(3)->create(['date' => today(), 'status' => 'waiting']);
    Appointment::factory()->create(['date' => today()->subDay(), 'status' => 'waiting']); // not today

    Livewire::test(Dashboard::class)
        ->assertSee('3'); // total today
});

test('Dashboard counts waiting appointments correctly', function () {
    actingAs(dashAdmin());

    Appointment::factory()->waiting()->create(['date' => today()]);
    Appointment::factory()->done()->create(['date' => today()]);

    Livewire::test(Dashboard::class)
        ->assertViewHas('waitingCount', 1);
});

test('Dashboard counts in_progress appointments correctly', function () {
    actingAs(dashAdmin());

    Appointment::factory()->inProgress()->create(['date' => today()]);
    Appointment::factory()->waiting()->create(['date' => today()]);

    Livewire::test(Dashboard::class)
        ->assertViewHas('inProgressCount', 1);
});

test('Dashboard counts done appointments correctly', function () {
    actingAs(dashAdmin());

    Appointment::factory()->done()->create(['date' => today()]);
    Appointment::factory()->done()->create(['date' => today()]);

    Livewire::test(Dashboard::class)
        ->assertViewHas('doneCount', 2);
});

// ─── Row 2: Financial widgets ─────────────────────────────────────────────────

test('Admin sees financial widgets', function () {
    actingAs(dashAdmin());

    Livewire::test(Dashboard::class)
        ->assertSee('Pendapatan Bulan Ini');
});

test('Non-admin does not see financial widgets', function () {
    actingAs(dashDoctor());

    Livewire::test(Dashboard::class)
        ->assertDontSee('Pendapatan Bulan Ini');
});

test('Dashboard calculates this month revenue correctly', function () {
    actingAs(dashAdmin());

    Invoice::factory()->fullyPaid()->create([
        'total_amount' => 200000,
        'paid_at' => now(),
    ]);
    Invoice::factory()->fullyPaid()->create([
        'total_amount' => 100000,
        'paid_at' => now()->subMonth(), // last month — should not count
    ]);

    Livewire::test(Dashboard::class)
        ->assertViewHas('revenueThisMonth', 200000.0);
});

test('Dashboard calculates last month revenue correctly', function () {
    actingAs(dashAdmin());

    Invoice::factory()->fullyPaid()->create([
        'total_amount' => 150000,
        'paid_at' => now()->subMonth(),
    ]);

    Livewire::test(Dashboard::class)
        ->assertViewHas('revenueLastMonth', 150000.0);
});

test('Dashboard counts unpaid invoices correctly', function () {
    actingAs(dashAdmin());

    Invoice::factory()->unpaid()->create();
    Invoice::factory()->unpaid()->create();
    Invoice::factory()->fullyPaid()->create();

    Livewire::test(Dashboard::class)
        ->assertViewHas('unpaidCount', 2);
});

test('Dashboard revenue trend is up when this month exceeds last month', function () {
    actingAs(dashAdmin());

    Invoice::factory()->fullyPaid()->create(['total_amount' => 100000, 'paid_at' => now()]);
    Invoice::factory()->fullyPaid()->create(['total_amount' => 50000, 'paid_at' => now()->subMonth()]);

    Livewire::test(Dashboard::class)
        ->assertViewHas('revenueTrend', fn ($trend) => $trend['direction'] === 'up');
});

// ─── Row 3: Doctors on duty ───────────────────────────────────────────────────

test('Dashboard shows doctors with schedule for today', function () {
    actingAs(dashAdmin());

    $dayName = match (today()->isoWeekday()) {
        1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis',
        5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu', default => 'Senin',
    };

    $doctor = Doctor::factory()->create(['is_active' => true]);
    DoctorSchedule::create([
        'doctor_id' => $doctor->id,
        'day_of_week' => $dayName,
        'start_time' => '08:00:00',
        'end_time' => '12:00:00',
        'slot_duration_minutes' => 30,
        'is_active' => 1,
    ]);

    Livewire::test(Dashboard::class)
        ->assertSee($doctor->name);
});

// ─── Row 4: Recent appointments ───────────────────────────────────────────────

test('Dashboard shows up to 5 recent appointments', function () {
    actingAs(dashAdmin());

    Appointment::factory()->count(7)->create(['date' => today()]);

    Livewire::test(Dashboard::class)
        ->assertViewHas('recentAppointments', fn ($apts) => $apts->count() === 5);
});

test('Dashboard shows link to appointments index', function () {
    actingAs(dashAdmin());

    Livewire::test(Dashboard::class)
        ->assertSee(route('appointments.index'));
});
