<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Anak;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserDetail>
 */
class AnakFactory extends Factory
{
    protected $model = Anak::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'nama' => $this->faker->name(), 
            'username' => $this->faker->firstName(), 
            'jk' => $this->faker->randomElement(['L', 'P']), 
            'kelompok_id' => $this->faker->randomElement(['1', '2']), 
            'tmp_lahir' => $this->faker->city(), 
            'jarak' => $this->faker->randomNumber(2, false), 
            'tgl_lahir' => $this->faker->date(), 
            'anak_ke' => $this->faker->randomDigitNot(0), 
            'alamat' => $this->faker->address(), 
            'status' => 'Aktif', 
            'isAntarJemput' => 0, 
            'isLaundry' => 0, 
            'sosialisasi_dengan_lingkungan' => 'Ceria', 
            'sakit_yang_pernah_diderita' => '', 
            'makanan_yang_disukai' =>'', 
            'makanan_yang_tidak_disukai' => '', 
            'alergi' => '', 
            'scan_akte' => 'scan/r3LvdlkdeQoFIlMilDdeGVIw1evnpKsz.jpg',
        ];
    }
}
