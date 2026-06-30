<?php

namespace App\Livewire\Medical;

use App\Models\Appointment;
use Livewire\Component;
use Livewire\WithPagination;

class PatientSearch extends Component
{
    use WithPagination;

    public string $search = '';

    /**
     * Reset pagination when search changes.
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Mount the component with role guard.
     */
    public function mount(): void
    {
        if (! in_array(auth()->user()->role, ['admin', 'doctor'])) {
            abort(403);
        }
    }

    /**
     * Render the component.
     */
    public function render()
    {
        if (! in_array(auth()->user()->role, ['admin', 'doctor'])) {
            abort(403);
        }

        $query = Appointment::query()
            ->whereNotNull('patient_id');

        if (trim($this->search) !== '') {
            $query->where(function ($q) {
                $q->where('patient_name', 'like', '%'.$this->search.'%')
                    ->orWhere('patient_id', 'like', '%'.$this->search.'%');
            });
        }

        $results = $query->selectRaw('patient_id, MAX(patient_name) as patient_name, COUNT(*) as visit_count, MAX(date) as latest_visit')
            ->groupBy('patient_id')
            ->orderBy('latest_visit', 'desc')
            ->paginate(10);

        return view('livewire.medical.patient-search', [
            'results' => $results,
        ])->layout('layouts.app');
    }
}
