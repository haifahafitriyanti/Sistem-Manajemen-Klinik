<?php

namespace App\Livewire\Medical;

use App\Models\Appointment;
use App\Models\MedicalRecord;
use Livewire\Component;

class MedicalRecordForm extends Component
{
    public int $appointmentId;

    public string $complaint = '';

    public string $diagnosis = '';

    public string $prescription = '';

    public string $notes = '';

    public Appointment $appointment;

    /** Whether a medical record already exists for this appointment. */
    public bool $alreadySaved = false;

    /**
     * Mount and load the appointment with its relations.
     */
    public function mount(int $appointmentId): void
    {
        if (! in_array(auth()->user()->role, ['admin', 'doctor'])) {
            abort(403);
        }

        $this->appointmentId = $appointmentId;
        $this->appointment = Appointment::with(['doctor', 'medicalRecord'])->findOrFail($appointmentId);

        if ($this->appointment->medicalRecord) {
            $this->alreadySaved = true;
            $record = $this->appointment->medicalRecord;
            $this->complaint = $record->complaint ?? '';
            $this->diagnosis = $record->diagnosis ?? '';
            $this->prescription = $record->prescription ?? '';
            $this->notes = $record->notes ?? '';
        } else {
            $this->complaint = $this->appointment->complaint ?? '';
        }
    }

    /**
     * Validation rules.
     *
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'complaint' => ['nullable', 'string'],
            'diagnosis' => ['required', 'string', 'min:3'],
            'prescription' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ];
    }

    /**
     * Save the medical record and mark the appointment as done.
     */
    public function save(): void
    {
        if (! in_array(auth()->user()->role, ['admin', 'doctor'])) {
            abort(403);
        }

        if ($this->appointment->status !== 'in_progress') {
            return;
        }

        if ($this->alreadySaved) {
            return;
        }

        $validated = $this->validate();

        MedicalRecord::create([
            'appointment_id' => $this->appointment->id,
            'doctor_id' => $this->appointment->doctor_id,
            'complaint' => $validated['complaint'] ?: null,
            'diagnosis' => $validated['diagnosis'],
            'prescription' => $validated['prescription'] ?: null,
            'notes' => $validated['notes'] ?: null,
        ]);

        $this->appointment->markAsDone();

        session()->flash('success', 'Pemeriksaan berhasil disimpan.');

        $this->redirect(route('appointments.index'));
    }

    /**
     * Render the component.
     */
    public function render()
    {
        if (! in_array(auth()->user()->role, ['admin', 'doctor'])) {
            abort(403);
        }

        return view('livewire.medical.medical-record-form', [
            'appointment' => $this->appointment,
        ])->layout('layouts.app');
    }
}
