<?php

namespace Database\Factories;

use App\Models\Doctor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Doctor>
 */
class DoctorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $specializations = [
            'Spesialis Anak',
            'Spesialis Penyakit Dalam',
            'Spesialis Kandungan',
            'Spesialis Bedah',
            'Dokter Umum',
            'Spesialis Kulit',
            'Spesialis Saraf',
        ];

        return [
            'name' => 'Dr. '.fake()->name(),
            'specialization' => fake()->randomElement($specializations),
            'license_number' => strtoupper(fake()->bothify('DS-#####-???')),
            'phone' => fake()->numerify('08##########'),
            'consultation_fee' => fake()->randomElement([75000, 100000, 150000, 175000, 200000]),
            'photo' => null,
            'is_active' => true,
        ];
    }
}
