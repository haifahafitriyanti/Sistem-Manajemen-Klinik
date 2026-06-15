<?php

namespace App\Livewire\Doctor;

use App\Models\Doctor;
use Livewire\Attributes\On;
use Livewire\Component;

class DoctorIndex extends Component
{
    public string $search = '';

    public bool $showForm = false;

    public ?int $editingDoctorId = null;

    /**
     * Open the form modal for creating a new doctor.
     */
    public function openCreate(): void
    {
        $this->editingDoctorId = null;
        $this->showForm = true;
    }

    /**
     * Open the form modal for editing a doctor.
     */
    public function openEdit(int $doctorId): void
    {
        $this->editingDoctorId = $doctorId;
        $this->showForm = true;
    }

    /**
     * Close the form modal.
     */
    public function closeForm(): void
    {
        $this->showForm = false;
        $this->editingDoctorId = null;
    }

    /**
     * Toggle the active status of a doctor.
     */
    public function toggleActive(int $doctorId): void
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $doctor = Doctor::findOrFail($doctorId);
        $doctor->update(['is_active' => ! $doctor->is_active]);
    }

    /**
     * Delete a doctor.
     */
    public function delete(int $doctorId): void
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        Doctor::findOrFail($doctorId)->delete();
    }

    /**
     * Handle the 'doctor-saved' event from DoctorForm.
     */
    #[On('doctor-saved')]
    public function onDoctorSaved(): void
    {
        $this->closeForm();
    }

    /**
     * Render the component.
     */
    public function render()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $doctors = Doctor::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('specialization', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy('name')
            ->get();

        return view('livewire.doctor.doctor-index', compact('doctors'))
            ->layout('layouts.app');
    }
}
