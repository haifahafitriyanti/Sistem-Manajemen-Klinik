<?php

namespace App\Livewire\Medical;

use App\Models\Appointment;
use Livewire\Component;

class PatientHistory extends Component
{
    public string $patientId;

    /** @var array<int, bool> Maps appointment ID → expanded state */
    public array $expanded = [];

    /**
     * Mount the component.
     */
    public function mount(string $patientId): void
    {
        if (! in_array(auth()->user()->role, ['admin', 'doctor'])) {
            abort(403);
        }

        $this->patientId = $patientId;
    }

    /**
     * Toggle accordion row expansion.
     */
    public function toggle(int $appointmentId): void
    {
        $this->expanded[$appointmentId] = ! ($this->expanded[$appointmentId] ?? false);
    }

    /**
     * Render the component.
     */
    public function render()
    {
        if (! in_array(auth()->user()->role, ['admin', 'doctor'])) {
            abort(403);
        }

        $appointments = Appointment::with(['doctor', 'medicalRecord'])
            ->where('patient_id', $this->patientId)
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get();

        // Use the first matching appointment for patient display name
        $patientName = $appointments->first()?->patient_name ?? $this->patientId;

        return view('livewire.medical.patient-history', compact('appointments', 'patientName'))
            ->layout('layouts.app');
    }
}
