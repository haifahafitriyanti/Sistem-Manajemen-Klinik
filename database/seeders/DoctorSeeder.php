<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\DoctorCategory;
use App\Models\DoctorSchedule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Load categories by slug for easy lookup
        $cats = DoctorCategory::pluck('id', 'slug');

        $doctors = [
            [
                'name'             => 'Dr. Andi Pratama, Sp.A',
                'slug'             => 'andi-pratama',
                'specialization'   => 'Spesialis Anak',
                'license_number'   => 'DS-12345-ANAK',
                'phone'            => '081234567890',
                'consultation_fee' => 150000,
                'bio'              => 'Dokter spesialis anak berpengalaman dalam tumbuh kembang anak dan penyakit pediatri umum.',
                'years_experience' => 8,
                'is_active'        => 1,
                'category_slug'    => 'spesialis-anak',
                'schedules'        => [
                    ['day_of_week' => 'Senin',  'start_time' => '08:00:00', 'end_time' => '12:00:00', 'slot_duration_minutes' => 30],
                    ['day_of_week' => 'Rabu',   'start_time' => '13:00:00', 'end_time' => '17:00:00', 'slot_duration_minutes' => 30],
                ],
            ],
            [
                'name'             => 'Dr. Budi Santoso, Sp.PD',
                'slug'             => 'budi-santoso',
                'specialization'   => 'Spesialis Penyakit Dalam',
                'license_number'   => 'DS-67890-DALAM',
                'phone'            => '081345678901',
                'consultation_fee' => 200000,
                'bio'              => 'Ahli penyakit dalam dengan fokus pada diabetes, hipertensi, dan penyakit metabolik.',
                'years_experience' => 12,
                'is_active'        => 1,
                'category_slug'    => 'spesialis-penyakit-dalam',
                'schedules'        => [
                    ['day_of_week' => 'Selasa', 'start_time' => '09:00:00', 'end_time' => '14:00:00', 'slot_duration_minutes' => 30],
                    ['day_of_week' => 'Kamis',  'start_time' => '09:00:00', 'end_time' => '14:00:00', 'slot_duration_minutes' => 30],
                ],
            ],
            [
                'name'             => 'Dr. Citra Lestari, Sp.OG',
                'slug'             => 'citra-lestari',
                'specialization'   => 'Spesialis Kandungan',
                'license_number'   => 'DS-54321-OBGYN',
                'phone'            => '081456789012',
                'consultation_fee' => 180000,
                'bio'              => 'Spesialis obstetri dan ginekologi untuk pemeriksaan kehamilan, persalinan, dan kesehatan reproduksi wanita.',
                'years_experience' => 10,
                'is_active'        => 1,
                'category_slug'    => 'spesialis-kandungan',
                'schedules'        => [
                    ['day_of_week' => 'Rabu',   'start_time' => '08:00:00', 'end_time' => '12:00:00', 'slot_duration_minutes' => 30],
                    ['day_of_week' => 'Jumat',  'start_time' => '13:00:00', 'end_time' => '17:00:00', 'slot_duration_minutes' => 30],
                ],
            ],
            [
                'name'             => 'Dr. Dian Wahyudi, Sp.B',
                'slug'             => 'dian-wahyudi',
                'specialization'   => 'Spesialis Bedah',
                'license_number'   => 'DS-11111-BEDAH',
                'phone'            => '081567890123',
                'consultation_fee' => 250000,
                'bio'              => 'Dokter bedah umum berpengalaman dalam tindakan operasi minor dan mayor.',
                'years_experience' => 15,
                'is_active'        => 1,
                'category_slug'    => 'spesialis-bedah',
                'schedules'        => [
                    ['day_of_week' => 'Senin',   'start_time' => '14:00:00', 'end_time' => '18:00:00', 'slot_duration_minutes' => 30],
                    ['day_of_week' => 'Kamis',   'start_time' => '13:00:00', 'end_time' => '17:00:00', 'slot_duration_minutes' => 30],
                ],
            ],
            [
                'name'             => 'Dr. Eka Surya, Sp.JP',
                'slug'             => 'eka-surya',
                'specialization'   => 'Spesialis Jantung',
                'license_number'   => 'DS-22222-JANTUNG',
                'phone'            => '081678901234',
                'consultation_fee' => 300000,
                'bio'              => 'Kardiolog berpengalaman dalam penanganan penyakit jantung koroner dan hipertensi.',
                'years_experience' => 18,
                'is_active'        => 1,
                'category_slug'    => 'spesialis-jantung',
                'schedules'        => [
                    ['day_of_week' => 'Selasa',  'start_time' => '08:00:00', 'end_time' => '12:00:00', 'slot_duration_minutes' => 30],
                    ['day_of_week' => 'Sabtu',   'start_time' => '08:00:00', 'end_time' => '11:00:00', 'slot_duration_minutes' => 30],
                ],
            ],
            [
                'name'             => 'Dr. Farah Nirmala, Sp.N',
                'slug'             => 'farah-nirmala',
                'specialization'   => 'Spesialis Saraf',
                'license_number'   => 'DS-33333-SARAF',
                'phone'            => '081789012345',
                'consultation_fee' => 220000,
                'bio'              => 'Neurolog spesialis gangguan sistem saraf termasuk stroke, migrain, dan epilepsi.',
                'years_experience' => 9,
                'is_active'        => 1,
                'category_slug'    => 'spesialis-saraf',
                'schedules'        => [
                    ['day_of_week' => 'Rabu',    'start_time' => '09:00:00', 'end_time' => '13:00:00', 'slot_duration_minutes' => 30],
                    ['day_of_week' => 'Jumat',   'start_time' => '08:00:00', 'end_time' => '12:00:00', 'slot_duration_minutes' => 30],
                ],
            ],
        ];

        foreach ($doctors as $doctorData) {
            $schedules     = $doctorData['schedules'];
            $categorySlug  = $doctorData['category_slug'];

            unset($doctorData['schedules'], $doctorData['category_slug']);

            $doctorData['doctor_category_id'] = $cats[$categorySlug] ?? null;

            /** @var Doctor $doctor */
            $doctor = Doctor::firstOrCreate(
                ['license_number' => $doctorData['license_number']],
                $doctorData
            );

            // Ensure slug is set if not already (idempotent re-seed)
            if (! $doctor->slug) {
                $doctor->slug = Str::slug($doctor->name);
                $doctor->save();
            }

            foreach ($schedules as $sched) {
                DoctorSchedule::firstOrCreate(
                    [
                        'doctor_id'   => $doctor->id,
                        'day_of_week' => $sched['day_of_week'],
                    ],
                    array_merge($sched, [
                        'doctor_id' => $doctor->id,
                        'is_active' => 1,
                    ])
                );
            }
        }
    }
}
