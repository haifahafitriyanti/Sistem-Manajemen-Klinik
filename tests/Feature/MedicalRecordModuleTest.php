<?php

use App\Livewire\Medical\MedicalRecordForm;
use App\Livewire\Medical\PatientHistory;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

// ─── Helpers ─────────────────────────────────────────────────────────────────

function medAdmin(): User
{
    return User::factory()->create(['role' => 'admin', 'is_active' => 1]);
}

function medDoctor(): User
{
    return User::factory()->create(['role' => 'doctor', 'is_active' => 1]);
}

function medReceptionist(): User
{
    return User::factory()->create(['role' => 'receptionist', 'is_active' => 1]);
}

function inProgressAppointment(): Appointment
{
    return Appointment::factory()->inProgress()->create([
        'date' => today()->toDateString(),
        'complaint' => 'Kepala pusing',
    ]);
}

// ─── Route access ─────────────────────────────────────────────────────────────

test('admin can access examine page', function () {
    $apt = inProgressAppointment();

    actingAs(medAdmin())
        ->get(route('appointments.examine', $apt->id))
        ->assertOk();
});

test('doctor can access examine page', function () {
    $apt = inProgressAppointment();

    actingAs(medDoctor())
        ->get(route('appointments.examine', $apt->id))
        ->assertOk();
});

test('receptionist gets 403 on examine page', function () {
    $apt = inProgressAppointment();

    actingAs(medReceptionist())
        ->get(route('appointments.examine', $apt->id))
        ->assertForbidden();
});

// ─── MedicalRecordForm component ─────────────────────────────────────────────

test('MedicalRecordForm pre-fills complaint from appointment', function () {
    actingAs(medAdmin());

    $apt = inProgressAppointment();

    Livewire::test(MedicalRecordForm::class, ['appointmentId' => $apt->id])
        ->assertSet('complaint', 'Kepala pusing');
});

test('MedicalRecordForm shows error guard for non-in_progress appointment', function () {
    actingAs(medAdmin());

    $apt = Appointment::factory()->waiting()->create(['date' => today()]);

    $component = Livewire::test(MedicalRecordForm::class, ['appointmentId' => $apt->id]);

    expect($component->get('appointment')->status)->toBe('waiting');
});

test('MedicalRecordForm saves medical record and marks appointment done', function () {
    actingAs(medAdmin());

    $apt = inProgressAppointment();

    Livewire::test(MedicalRecordForm::class, ['appointmentId' => $apt->id])
        ->set('diagnosis', 'Hipertensi grade 1')
        ->set('prescription', 'Amlodipine 5mg 1x1')
        ->set('notes', 'Kontrol 2 minggu lagi')
        ->call('save');

    $apt->refresh();
    expect($apt->status)->toBe('done');
    expect(MedicalRecord::where('appointment_id', $apt->id)->exists())->toBeTrue();
});

test('MedicalRecordForm validation requires diagnosis', function () {
    actingAs(medAdmin());

    $apt = inProgressAppointment();

    Livewire::test(MedicalRecordForm::class, ['appointmentId' => $apt->id])
        ->set('diagnosis', '')
        ->call('save')
        ->assertHasErrors(['diagnosis']);
});

test('MedicalRecordForm shows read-only when record already exists', function () {
    actingAs(medAdmin());

    $apt = Appointment::factory()->done()->create(['date' => today()]);

    MedicalRecord::create([
        'appointment_id' => $apt->id,
        'doctor_id' => $apt->doctor_id,
        'diagnosis' => 'Flu biasa',
        'complaint' => 'Batuk pilek',
    ]);

    Livewire::test(MedicalRecordForm::class, ['appointmentId' => $apt->id])
        ->assertSet('alreadySaved', true)
        ->assertSet('diagnosis', 'Flu biasa');
});

test('MedicalRecordForm does not allow re-saving an already completed record', function () {
    actingAs(medAdmin());

    $apt = Appointment::factory()->done()->create(['date' => today()]);

    MedicalRecord::create([
        'appointment_id' => $apt->id,
        'doctor_id' => $apt->doctor_id,
        'diagnosis' => 'Flu biasa',
    ]);

    Livewire::test(MedicalRecordForm::class, ['appointmentId' => $apt->id])
        ->set('diagnosis', 'Changed diagnosis')
        ->call('save');

    // Still only 1 record — no duplicate
    expect(MedicalRecord::where('appointment_id', $apt->id)->count())->toBe(1);
});

// ─── PatientHistory component ────────────────────────────────────────────────

test('PatientHistory shows appointments for given patient_id', function () {
    actingAs(medAdmin());

    $apt1 = Appointment::factory()->done()->create(['patient_id' => 'KTP-XYZ', 'date' => today()]);
    $apt2 = Appointment::factory()->done()->create(['patient_id' => 'KTP-OTHER', 'date' => today()]);

    MedicalRecord::create([
        'appointment_id' => $apt1->id,
        'doctor_id' => $apt1->doctor_id,
        'diagnosis' => 'Diagnosis XYZ',
    ]);

    Livewire::test(PatientHistory::class, ['patientId' => 'KTP-XYZ'])
        ->assertSee($apt1->patient_name)
        ->assertDontSee($apt2->patient_name);
});

test('PatientHistory toggling expands accordion', function () {
    actingAs(medAdmin());

    $apt = Appointment::factory()->done()->create(['patient_id' => 'KTP-ACC', 'date' => today()]);

    Livewire::test(PatientHistory::class, ['patientId' => 'KTP-ACC'])
        ->call('toggle', $apt->id)
        ->assertSet("expanded.{$apt->id}", true)
        ->call('toggle', $apt->id)
        ->assertSet("expanded.{$apt->id}", false);
});

// ─── Appointment model ────────────────────────────────────────────────────────

test('Appointment markAsDone changes status to done', function () {
    $apt = Appointment::factory()->inProgress()->create(['date' => today()]);

    $apt->markAsDone();

    expect($apt->fresh()->status)->toBe('done');
});

test('Appointment hasOne MedicalRecord relation works', function () {
    $apt = Appointment::factory()->done()->create(['date' => today()]);

    MedicalRecord::create([
        'appointment_id' => $apt->id,
        'doctor_id' => $apt->doctor_id,
        'diagnosis' => 'Test diagnosis',
    ]);

    expect($apt->medicalRecord)->not->toBeNull();
    expect($apt->medicalRecord->diagnosis)->toBe('Test diagnosis');
});
