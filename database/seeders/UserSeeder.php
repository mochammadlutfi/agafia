<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        \Faker\Factory::create('id_ID');

        for ($i = 0; $i < 10; $i++) {
            User::create([
                'nama' => fake('id_ID')->name(),
                'email' => fake('id_ID')->unique()->safeEmail(),
                'email_verified_at' => now(),
                'password' => Hash::make('123456'),
                'aktif' => true,
                'status' => fake()->randomElement(['draft', 'tervalidasi', 'sudah_interview', 'medical', 'pelatihan', 'siap']),
            ]);
        }
    }
}
