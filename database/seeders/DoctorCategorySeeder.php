<?php

namespace Database\Seeders;

use App\Models\DoctorCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DoctorCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name'        => 'Spesialis Anak',
                'description' => 'Dokter spesialis kesehatan bayi, anak, dan remaja.',
                'icon'        => 'baby',
            ],
            [
                'name'        => 'Spesialis Penyakit Dalam',
                'description' => 'Menangani penyakit organ dalam seperti diabetes, hipertensi, dan gangguan metabolisme.',
                'icon'        => 'stethoscope',
            ],
            [
                'name'        => 'Spesialis Kandungan',
                'description' => 'Dokter spesialis obstetri dan ginekologi untuk kesehatan reproduksi wanita.',
                'icon'        => 'heart-pulse',
            ],
            [
                'name'        => 'Spesialis Bedah',
                'description' => 'Menangani tindakan operasi dan bedah umum.',
                'icon'        => 'scissors',
            ],
            [
                'name'        => 'Spesialis Jantung',
                'description' => 'Dokter spesialis kardiologi untuk penyakit jantung dan pembuluh darah.',
                'icon'        => 'heart',
            ],
            [
                'name'        => 'Spesialis Saraf',
                'description' => 'Menangani gangguan sistem saraf seperti stroke, migrain, dan epilepsi.',
                'icon'        => 'brain',
            ],
        ];

        foreach ($categories as $cat) {
            DoctorCategory::firstOrCreate(
                ['slug' => Str::slug($cat['name'])],
                array_merge($cat, [
                    'slug'      => Str::slug($cat['name']),
                    'is_active' => 1,
                ])
            );
        }
    }
}
