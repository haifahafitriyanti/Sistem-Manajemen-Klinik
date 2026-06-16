<?php

namespace App\Livewire\Appointment;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use Carbon\Carbon;
use Livewire\Component;

class AppointmentForm extends Component
{
    public string $doctor_id = '';

    public string $patient_name = '';

    public string $patient_id = '';

    public string $phone = '';

    public string $date = '';

    public string $time_slot = '';

    public string $complaint = '';

    /** @var array<string> Available time slots for the selected doctor/date combo */
    public array $availableSlots = [];

    public string $noScheduleMessage = '';

    /** @var array<int, array{id: int, name: string}> */
    public array $doctors = [];

    /**
     * Boot: load doctors once.
     */
    public function mount(): void
    {
        $this->doctors = Doctor::active()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($d) => ['id' => $d->id, 'name' => $d->name])
            ->toArray();

        $this->date = today()->toDateString();
    }

    /**
     * React to doctor or date changes and reload slots.
     */
    public function updated(string $field): void
    {
        if (in_array($field, ['doctor_id', 'date'])) {
            $this->time_slot = '';
            $this->loadAvailableSlots();
        }
    }

    /**
     * Load available time slots for the selected doctor on the selected date.
     */
    public function loadAvailableSlots(): void
    {
        $this->availableSlots = [];
        $this->noScheduleMessage = '';

        if (! $this->doctor_id || ! $this->date) {
            return;
        }

        $date = Carbon::parse($this->date);

        // Map Carbon day-of-week (1=Mon…7=Sun) to Indonesian names
        $dayMap = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
        ];
        $dayName = $dayMap[$date->isoWeekday()];

        $schedule = DoctorSchedule::where('doctor_id', $this->doctor_id)
            ->where('day_of_week', $dayName)
            ->where('is_active', 1)
            ->first();

        if (! $schedule) {
            $this->noScheduleMessage = "Dokter tidak praktik pada hari {$dayName}.";

            return;
        }

        // Generate all slots from start_time to end_time
        $allSlots = $this->generateSlots(
            $schedule->start_time,
            $schedule->end_time,
            $schedule->slot_duration_minutes
        );

        // Fetch taken slots (not cancelled)
        $takenSlots = Appointment::where('doctor_id', $this->doctor_id)
            ->whereDate('date', $this->date)
            ->whereIn('status', ['waiting', 'in_progress', 'done'])
            ->pluck('time_slot')
            ->toArray();

        $this->availableSlots = array_values(array_diff($allSlots, $takenSlots));

        if (empty($this->availableSlots)) {
            $this->noScheduleMessage = 'Semua slot sudah penuh untuk hari ini.';
        }
    }

    /**
     * Generate time slots between start and end with the given interval.
     *
     * @return array<string>
     */
    private function generateSlots(string $startTime, string $endTime, int $intervalMinutes): array
    {
        $slots = [];
        $current = Carbon::createFromTimeString($startTime);
        $end = Carbon::createFromTimeString($endTime);

        // Last slot must start before end_time (a slot at end_time has no duration)
        while ($current->lt($end)) {
            $slots[] = $current->format('H:i');
            $current->addMinutes($intervalMinutes);
        }

        return $slots;
    }

    /**
     * Validation rules.
     *
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'doctor_id' => ['required', 'integer', 'exists:doctors,id'],
            'patient_name' => ['required', 'string', 'max:100'],
            'patient_id' => ['nullable', 'string', 'max:20'],
            'phone' => ['required', 'string', 'max:20'],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'time_slot' => ['required', 'string', 'in:'.implode(',', $this->availableSlots ?: ['__none__'])],
            'complaint' => ['nullable', 'string'],
        ];
    }

    /**
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'time_slot.in' => 'Slot waktu yang dipilih tidak tersedia.',
            'date.after_or_equal' => 'Tanggal tidak boleh di masa lalu.',
        ];
    }

    /**
     * Save the new appointment.
     */
    public function save(): void
    {
        if (! in_array(auth()->user()->role, ['admin', 'receptionist', 'doctor'])) {
            abort(403);
        }

        // Reload slots right before validation so the rule has fresh data
        $this->loadAvailableSlots();

        $this->validate();

        $appointment = new Appointment([
            'doctor_id' => $this->doctor_id,
            'patient_name' => $this->patient_name,
            'patient_id' => $this->patient_id ?: null,
            'phone' => $this->phone,
            'date' => $this->date,
            'time_slot' => $this->time_slot,
            'complaint' => $this->complaint ?: null,
            'status' => 'waiting',
        ]);

        $appointment->queue_number = $appointment->generateQueueNumber();
        $appointment->save();

        $this->dispatch('appointment-saved');

        $this->reset(['patient_name', 'patient_id', 'phone', 'time_slot', 'complaint']);
        $this->loadAvailableSlots();
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.appointment.appointment-form');
    }
}
