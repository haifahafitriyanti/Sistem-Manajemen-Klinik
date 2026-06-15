<?php

namespace App\Livewire\Doctor;

use App\Models\Doctor;
use Livewire\Component;
use Livewire\WithFileUploads;

class DoctorForm extends Component
{
    use WithFileUploads;

    public ?int $doctorId = null;

    public string $name = '';

    public string $specialization = '';

    public ?string $license_number = null;

    public ?string $phone = null;

    public float $consultation_fee = 0;

    public bool $is_active = true;

    public $photo;

    public ?string $existingPhoto = null;

    /**
     * Get the validation rules.
     */
    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'specialization' => 'required|string|max:100',
            'license_number' => 'nullable|string|max:50|unique:doctors,license_number,'.($this->doctorId ?? 'NULL').',id',
            'phone' => 'nullable|string|max:20',
            'consultation_fee' => 'required|numeric|min:0',
            'is_active' => 'required|boolean',
            'photo' => 'nullable|image|max:2048', // Max 2MB
        ];
    }

    /**
     * Mount the component.
     */
    public function mount(?int $doctorId = null): void
    {
        if ($doctorId) {
            $this->doctorId = $doctorId;
            $doctor = Doctor::findOrFail($doctorId);
            $this->name = $doctor->name;
            $this->specialization = $doctor->specialization;
            $this->license_number = $doctor->license_number;
            $this->phone = $doctor->phone;
            $this->consultation_fee = (float) $doctor->consultation_fee;
            $this->is_active = (bool) $doctor->is_active;
            $this->existingPhoto = $doctor->photo;
        }
    }

    /**
     * Save doctor.
     */
    public function save(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'specialization' => $this->specialization,
            'license_number' => $this->license_number,
            'phone' => $this->phone,
            'consultation_fee' => $this->consultation_fee,
            'is_active' => $this->is_active ? 1 : 0,
        ];

        if ($this->photo) {
            $photoPath = $this->photo->store('doctors', 'public');
            $data['photo'] = $photoPath;
        }

        if ($this->doctorId) {
            $doctor = Doctor::findOrFail($this->doctorId);
            $doctor->update($data);
        } else {
            Doctor::create($data);
        }

        $this->dispatch('doctor-saved');
        $this->reset(['photo']);
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.doctor.doctor-form');
    }
}
