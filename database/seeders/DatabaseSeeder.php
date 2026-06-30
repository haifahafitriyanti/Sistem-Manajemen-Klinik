<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\AppointmentSeeder;
use Database\Seeders\DoctorCategorySeeder;
use Database\Seeders\DoctorSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin Klinik',
            'email' => 'admin@klinik.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_active' => 1,
        ]);

        $this->call([
            DoctorCategorySeeder::class,
            DoctorSeeder::class,
            AppointmentSeeder::class,
        ]);
    }
}
