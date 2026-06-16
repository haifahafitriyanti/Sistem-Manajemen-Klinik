<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Doctor;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $doctors = Doctor::orderBy('id')->take(3)->get();

        if ($doctors->isEmpty()) {
            $this->command->warn('No doctors found — run DoctorSeeder first.');

            return;
        }

        $today = today()->toDateString();

        $seeds = [
            [
                'doctor_id' => $doctors[0]->id,
                'patient_name' => 'Budi Raharjo',
                'patient_id' => 'KTP-001',
                'phone' => '081111111111',
                'date' => $today,
                'time_slot' => '08:00',
                'complaint' => 'Demam dan batuk sejak 3 hari',
                'status' => 'waiting',
                'queue_number' => 1,
            ],
            [
                'doctor_id' => $doctors[0]->id,
                'patient_name' => 'Siti Aminah',
                'patient_id' => 'KTP-002',
                'phone' => '082222222222',
                'date' => $today,
                'time_slot' => '08:30',
                'complaint' => 'Sakit kepala',
                'status' => 'waiting',
                'queue_number' => 2,
            ],
            [
                'doctor_id' => $doctors[1 % $doctors->count()]->id,
                'patient_name' => 'Andi Wijaya',
                'patient_id' => 'KTP-003',
                'phone' => '083333333333',
                'date' => $today,
                'time_slot' => '09:00',
                'complaint' => 'Kontrol tekanan darah',
                'status' => 'in_progress',
                'queue_number' => 1,
            ],
            [
                'doctor_id' => $doctors[2 % $doctors->count()]->id,
                'patient_name' => 'Dewi Kusuma',
                'patient_id' => 'KTP-004',
                'phone' => '084444444444',
                'date' => $today,
                'time_slot' => '08:00',
                'complaint' => 'Pemeriksaan kehamilan rutin',
                'status' => 'done',
                'queue_number' => 1,
            ],
            [
                'doctor_id' => $doctors[0]->id,
                'patient_name' => 'Hendra Saputra',
                'patient_id' => 'KTP-005',
                'phone' => '085555555555',
                'date' => $today,
                'time_slot' => '09:00',
                'complaint' => 'Mual dan muntah',
                'status' => 'cancelled',
                'queue_number' => null,
                'cancellation_reason' => 'Pasien tidak hadir',
            ],
        ];

        foreach ($seeds as $seed) {
            Appointment::create($seed);
        }
    }
}
