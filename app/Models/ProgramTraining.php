<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramTraining extends Model
{
    use HasFactory;

    protected $table = 'training_program';

    protected $fillable = [
        'nama',
        'deskripsi',
        'durasi',
        'lokasi',
        'kapasitas',
        'instruktur',
        'persyaratan',
        'aktif',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    protected $appends = [
        'jumlah_peserta_aktif',
        'sisa_kapasitas',
        'penuh',
    ];

    // Relationships
    public function training()
    {
        return $this->hasMany(Training::class, 'program_id');
    }

    public function trainingActive()
    {
        return $this->hasMany(Training::class, 'program_id')
                    ->whereIn('status', ['terdaftar', 'sedang_pelatihan']);
    }

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    public function scopeTersedia($query)
    {
        return $query->where('aktif', true)
                    ->whereHas('trainingActive', function($q) {
                        $q->havingRaw('COUNT(*) < kapasitas');
                    });
    }

    // Mutators & Accessors
    public function getJumlahPesertaAktifAttribute()
    {
        return $this->trainingActive()->count();
    }

    public function getSisaKapasitasAttribute()
    {
        return $this->kapasitas - $this->jumlah_peserta_aktif;
    }

    public function getPenuhAttribute()
    {
        return $this->jumlah_peserta_aktif >= $this->kapasitas;
    }
}
