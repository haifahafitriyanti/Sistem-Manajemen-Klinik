<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Doctor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'doctor_id' => Doctor::factory(),
            'patient_name' => fake()->name(),
            'patient_id' => fake()->numerify('KTP-###'),
            'phone' => fake()->numerify('08##########'),
            'date' => today()->toDateString(),
            'time_slot' => fake()->randomElement(['08:00', '08:30', '09:00', '09:30', '10:00']),
            'complaint' => fake()->sentence(),
            'diagnosis' => null,
            'status' => 'waiting',
            'queue_number' => fake()->numberBetween(1, 20),
            'cancellation_reason' => null,
        ];
    }

    /**
     * State: waiting.
     */
    public function waiting(): static
    {
        return $this->state(['status' => 'waiting']);
    }

    /**
     * State: in_progress.
     */
    public function inProgress(): static
    {
        return $this->state(['status' => 'in_progress']);
    }

    /**
     * State: done.
     */
    public function done(): static
    {
        return $this->state(['status' => 'done']);
    }

    /**
     * State: cancelled.
     */
    public function cancelled(): static
    {
        return $this->state([
            'status' => 'cancelled',
            'queue_number' => null,
            'cancellation_reason' => fake()->sentence(),
        ]);
    }
}
