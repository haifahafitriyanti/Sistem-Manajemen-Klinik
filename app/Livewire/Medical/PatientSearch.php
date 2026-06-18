<?php

namespace App\Livewire\Medical;

use App\Models\Appointment;
use Illuminate\Support\Collection;
use Livewire\Component;

class PatientSearch extends Component
{
    public string $search = '';

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
     * Get the live search results.
     *
     * @return Collection<int, object>
     */
    public function getResultsProperty(): Collection
    {
        if (trim($this->search) === '') {
            return collect();
        }

        return Appointment::query()
            ->whereNotNull('patient_id')
            ->where(function ($q) {
                $q->where('patient_name', 'like', '%'.$this->search.'%')
                    ->orWhere('patient_id', 'like', '%'.$this->search.'%');
            })
            ->selectRaw('patient_id, patient_name, COUNT(*) as visit_count')
            ->groupBy('patient_id', 'patient_name')
            ->orderBy('patient_name')
            ->limit(10)
            ->get();
    }

    /**
     * Render the component.
     */
    public function render()
    {
        if (! in_array(auth()->user()->role, ['admin', 'doctor'])) {
            abort(403);
        }

        return view('livewire.medical.patient-search', [
            'results' => $this->results,
        ])->layout('layouts.app');
    }
}
