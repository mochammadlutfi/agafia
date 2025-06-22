<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserDetail;
use Faker\Factory;

class UserDetailSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Factory::create('id_ID');

        foreach (User::all() as $user) {
            UserDetail::create([
                'user_id' => $user->id,
                'nik' => $faker->unique()->numerify('################'),
                'nama_lengkap' => $user->nama,
                'tempat_lahir' => $faker->city,
                'tanggal_lahir' => $faker->date('Y-m-d', '-18 years'),
                'jenis_kelamin' => $faker->randomElement(['laki_laki', 'perempuan']),
                'status_pernikahan' => $faker->randomElement(['belum_menikah', 'menikah', 'cerai', 'janda_duda']),
                'alamat' => $faker->address,
                'nomor_telepon' => $faker->phoneNumber,
                'nama_kontak_darurat' => $faker->name,
                'telepon_kontak_darurat' => $faker->phoneNumber,
                'tingkat_pendidikan' => $faker->randomElement(['SMA', 'D3', 'S1', 'S2']),
                'keahlian' => json_encode([$faker->jobTitle, $faker->jobTitle]),
                'pengalaman_kerja' => json_encode([
                    ['perusahaan' => $faker->company, 'jabatan' => $faker->jobTitle, 'tahun' => $faker->year],
                ]),
                'kemampuan_bahasa' => json_encode([
                    ['bahasa' => 'Inggris', 'tingkat' => 'menengah'],
                    ['bahasa' => 'Indonesia', 'tingkat' => 'mahir'],
                ]),
                'foto' => null,
                'status_pendaftaran' => $faker->randomElement(['draft', 'tervalidasi', 'sudah_interview', 'medical', 'pelatihan', 'siap']),
                'catatan' => $faker->sentence,
                'divalidasi_tanggal' => now(),
                'divalidasi_oleh' => User::inRandomOrder()->first()?->id,
            ]);
        }
    }
}
