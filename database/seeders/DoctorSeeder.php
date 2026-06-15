<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\DoctorSchedule;
use Illuminate\Database\Seeder;

class DoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $doctor1 = Doctor::create([
            'name' => 'Dr. Andi Pratama',
            'specialization' => 'Spesialis Anak',
            'license_number' => 'DS-12345-ANAK',
            'phone' => '081234567890',
            'consultation_fee' => 150000.00,
            'is_active' => 1,
        ]);

        DoctorSchedule::create([
            'doctor_id' => $doctor1->id,
            'day_of_week' => 'Senin',
            'start_time' => '08:00:00',
            'end_time' => '12:00:00',
            'slot_duration_minutes' => 30,
            'is_active' => 1,
        ]);

        DoctorSchedule::create([
            'doctor_id' => $doctor1->id,
            'day_of_week' => 'Rabu',
            'start_time' => '13:00:00',
            'end_time' => '17:00:00',
            'slot_duration_minutes' => 30,
            'is_active' => 1,
        ]);

        $doctor2 = Doctor::create([
            'name' => 'Dr. Budi Santoso',
            'specialization' => 'Spesialis Penyakit Dalam',
            'license_number' => 'DS-67890-DALAM',
            'phone' => '081345678901',
            'consultation_fee' => 200000.00,
            'is_active' => 1,
        ]);

        DoctorSchedule::create([
            'doctor_id' => $doctor2->id,
            'day_of_week' => 'Selasa',
            'start_time' => '09:00:00',
            'end_time' => '14:00:00',
            'slot_duration_minutes' => 30,
            'is_active' => 1,
        ]);

        DoctorSchedule::create([
            'doctor_id' => $doctor2->id,
            'day_of_week' => 'Kamis',
            'start_time' => '09:00:00',
            'end_time' => '14:00:00',
            'slot_duration_minutes' => 30,
            'is_active' => 1,
        ]);

        $doctor3 = Doctor::create([
            'name' => 'Dr. Citra Lestari',
            'specialization' => 'Spesialis Kandungan',
            'license_number' => 'DS-54321-OBGYN',
            'phone' => '081456789012',
            'consultation_fee' => 180000.00,
            'is_active' => 1,
        ]);

        DoctorSchedule::create([
            'doctor_id' => $doctor3->id,
            'day_of_week' => 'Rabu',
            'start_time' => '08:00:00',
            'end_time' => '12:00:00',
            'slot_duration_minutes' => 30,
            'is_active' => 1,
        ]);

        DoctorSchedule::create([
            'doctor_id' => $doctor3->id,
            'day_of_week' => 'Jumat',
            'start_time' => '13:00:00',
            'end_time' => '17:00:00',
            'slot_duration_minutes' => 30,
            'is_active' => 1,
        ]);
    }
}
