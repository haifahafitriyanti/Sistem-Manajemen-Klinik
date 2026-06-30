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

        User::firstOrCreate(
            ['email' => 'admin@klinik.com'],
            [
                'name' => 'Admin Klinik',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'is_active' => 1,
            ]
        );

        User::firstOrCreate(
            ['email' => 'kasir@klinik.com'],
            [
                'name' => 'Kasir Klinik',
                'password' => bcrypt('password'),
                'role' => 'cashier',
                'is_active' => 1,
            ]
        );

        User::firstOrCreate(
            ['email' => 'doctor@klinik.com'],
            [
                'name' => 'Dr. Andi Pratama, Sp.A',
                'password' => bcrypt('password'),
                'role' => 'doctor',
                'is_active' => 1,
            ]
        );

        User::firstOrCreate(
            ['email' => 'receptionist@klinik.com'],
            [
                'name' => 'Resepsionis Klinik',
                'password' => bcrypt('password'),
                'role' => 'receptionist',
                'is_active' => 1,
            ]
        );

        $this->call([
            DoctorCategorySeeder::class,
            DoctorSeeder::class,
            AppointmentSeeder::class,
        ]);
    }
}
