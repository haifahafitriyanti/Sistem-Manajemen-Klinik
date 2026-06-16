<?php

namespace App\Livewire\Appointment;

use App\Models\Appointment;
use App\Models\Doctor;
use Livewire\Attributes\On;
use Livewire\Component;

class AppointmentIndex extends Component
{
    public string $search = '';

    public string $filterDoctor = '';

    public string $filterStatus = '';

    public bool $showForm = false;

    /** @var int|null ID being cancelled — triggers inline cancellation reason input */
    public ?int $cancellingId = null;

    public string $cancellationReason = '';

    /**
     * Open the "Tambah Appointment" modal.
     */
    public function openCreate(): void
    {
        $this->showForm = true;
    }

    /**
     * Close the form modal.
     */
    public function closeForm(): void
    {
        $this->showForm = false;
    }

    /**
     * Handle appointment-saved event from AppointmentForm.
     */
    #[On('appointment-saved')]
    public function onAppointmentSaved(): void
    {
        $this->closeForm();
    }

    /**
     * Advance an appointment from waiting → in_progress.
     */
    public function startExamination(int $appointmentId): void
    {
        $this->authorizeAccess();

        Appointment::whereIn('status', ['waiting'])
            ->findOrFail($appointmentId)
            ->update(['status' => 'in_progress']);
    }

    /**
     * Show the inline cancellation reason input for an appointment.
     */
    public function initCancel(int $appointmentId): void
    {
        $this->cancellingId = $appointmentId;
        $this->cancellationReason = '';
    }

    /**
     * Abort an ongoing cancellation.
     */
    public function abortCancel(): void
    {
        $this->cancellingId = null;
        $this->cancellationReason = '';
    }

    /**
     * Confirm cancellation with an optional reason.
     */
    public function confirmCancel(int $appointmentId): void
    {
        $this->authorizeAccess();

        Appointment::whereIn('status', ['waiting', 'in_progress'])
            ->findOrFail($appointmentId)
            ->update([
                'status' => 'cancelled',
                'cancellation_reason' => $this->cancellationReason ?: null,
            ]);

        $this->cancellingId = null;
        $this->cancellationReason = '';
    }

    /**
     * Render the component.
     */
    public function render()
    {
        $this->authorizeAccess();

        $doctors = Doctor::active()->orderBy('name')->get();

        $appointments = Appointment::with('doctor')
            ->today()
            ->when($this->filterDoctor, fn ($q) => $q->where('doctor_id', $this->filterDoctor))
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->when($this->search, function ($q) {
                $q->where(function ($inner) {
                    $inner->where('patient_name', 'like', '%'.$this->search.'%')
                        ->orWhere('patient_id', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy('queue_number')
            ->get();

        return view('livewire.appointment.appointment-index', compact('appointments', 'doctors'))
            ->layout('layouts.app');
    }

    /**
     * Abort with 403 if the user lacks access.
     */
    private function authorizeAccess(): void
    {
        if (! in_array(auth()->user()->role, ['admin', 'receptionist', 'doctor'])) {
            abort(403);
        }
    }
}
