<?php

namespace Database\Factories;

use App\Models\UserDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserDetail>
 */
class UserDetailFactory extends Factory
{
    protected $model = UserDetail::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'nama_ayah' => $this->faker->name('male'),
            'tmp_lahir_ayah' => $this->faker->city(),
            'tgl_lahir_ayah' => $this->faker->date(),
            'telp_ayah' => $this->faker->phoneNumber(),
            'alamat_ayah' => $this->faker->address(),
            'pekerjaan_ayah' => $this->faker->randomElement(['Pegawai Swasta', 'Wiraswasta', 'PNS']),
            'penghasilan_ayah' => $this->faker->randomElement(['Tidak Berpenghasilan', '> Rp 2.000.000', 'Rp 2.000.000 - Rp 3.000.000', 'Rp 4.000.000 - Rp 5.000.000', 'Rp 5.000.000 - Rp 6.000.000']),
            'alamat_kantor_ayah' => $this->faker->address(),
            'pendidikan_ayah' => $this->faker->randomElement(['SMP', 'SMA/SMK', 'D3', 'S1', 'S2']),
            'agama_ayah' => 'ISLAM',
            'nama_ibu' => $this->faker->name('female'),
            'tmp_lahir_ibu' => $this->faker->city(),
            'tgl_lahir_ibu' => $this->faker->date(),
            'telp_ibu' => $this->faker->phoneNumber(),
            'alamat_ibu' => $this->faker->address(),
            'pekerjaan_ibu' => $this->faker->randomElement(['Pegawai Swasta','Wiraswasta', 'PNS']),
            'penghasilan_ibu' => $this->faker->randomElement(['Tidak Berpenghasilan', '> Rp 2.000.000', 'Rp 2.000.000 - Rp 3.000.000', 'Rp 4.000.000 - Rp 5.000.000', 'Rp 5.000.000 - Rp 6.000.000']),
            'alamat_kantor_ibu' => $this->faker->address(),
            'pendidikan_ibu' => $this->faker->randomElement(['SMP', 'SMA/SMK', 'D3', 'S1', 'S2']),
            'agama_ibu' => 'ISLAM',
            'scan_ktp_ayah' => 'scan/1kwndr6dNo2t5xB5IyFjCAG03BMg890f.jpg',
            'scan_ktp_ibu' => 'scan/Fo1OlHKqdRKSb1TflJbBin4IC06Yjpqb.jpg',
            'scan_kk' => 'scan/pjQfILhkfHex1HpTWWxTHvCMpJoK00GQ.jpg',
        ];
    }
}
