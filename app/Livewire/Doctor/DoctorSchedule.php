<?php

namespace App\Livewire\Doctor;

use App\Models\Doctor;
use App\Models\DoctorSchedule as DoctorScheduleModel;
use Livewire\Component;

class DoctorSchedule extends Component
{
    public int $doctorId;

    public string $day_of_week = '';

    public string $start_time = '';

    public string $end_time = '';

    public int $slot_duration_minutes = 30;

    /** @var array<string> */
    public array $daysOfWeek = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

    /**
     * Get the validation rules.
     */
    protected function rules(): array
    {
        return [
            'day_of_week' => [
                'required',
                'in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
                function (string $attribute, mixed $value, \Closure $fail) {
                    $exists = DoctorScheduleModel::where('doctor_id', $this->doctorId)
                        ->where('day_of_week', $value)
                        ->exists();
                    if ($exists) {
                        $fail('Jadwal untuk hari '.$value.' sudah ada untuk dokter ini.');
                    }
                },
            ],
            'start_time' => 'required|date_format:H:i',
            'end_time' => [
                'required',
                'date_format:H:i',
                function (string $attribute, mixed $value, \Closure $fail) {
                    if ($this->start_time && $value <= $this->start_time) {
                        $fail('Jam selesai harus lebih besar dari jam mulai.');
                    }
                },
            ],
            'slot_duration_minutes' => 'required|integer|min:5|max:120',
        ];
    }

    /**
     * Mount the component with the doctor ID from the route.
     */
    public function mount(int $id): void
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        Doctor::findOrFail($id);
        $this->doctorId = $id;
    }

    /**
     * Save a new schedule entry.
     */
    public function save(): void
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $this->validate();

        DoctorScheduleModel::create([
            'doctor_id' => $this->doctorId,
            'day_of_week' => $this->day_of_week,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'slot_duration_minutes' => $this->slot_duration_minutes,
            'is_active' => 1,
        ]);

        $this->reset(['day_of_week', 'start_time', 'end_time']);
        $this->slot_duration_minutes = 30;
    }

    /**
     * Delete a schedule entry.
     */
    public function deleteSchedule(int $scheduleId): void
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        DoctorScheduleModel::where('doctor_id', $this->doctorId)
            ->findOrFail($scheduleId)
            ->delete();
    }

    /**
     * Render the component.
     */
    public function render()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $doctor = Doctor::findOrFail($this->doctorId);
        $schedules = DoctorScheduleModel::where('doctor_id', $this->doctorId)
            ->get()
            ->sortBy(function ($schedule) {
                $order = ['Senin' => 0, 'Selasa' => 1, 'Rabu' => 2, 'Kamis' => 3, 'Jumat' => 4, 'Sabtu' => 5, 'Minggu' => 6];

                return $order[$schedule->day_of_week] ?? 7;
            })
            ->values();

        return view('livewire.doctor.doctor-schedule', compact('doctor', 'schedules'))
            ->layout('layouts.app');
    }
}
