<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilInterview extends Model
{
    use HasFactory;

    protected $table = 'hasil_interview';

    protected $fillable = [
        'jadwal_id',
        'talent_id',
        'skor_interview',
        'skor_psikologi',
        'kemampuan_komunikasi',
        'kemampuan_teknis',
        'penilaian_kepribadian',
        'rekomendasi',
        'catatan',
        'dinilai_oleh',
        'tanggal_penilaian',
        'disetujui_oleh',
        'tanggal_persetujuan',
    ];

    protected $casts = [
        'tanggal_penilaian' => 'datetime',
        'tanggal_persetujuan' => 'datetime',
    ];

    // Relationships
    public function jadwal()
    {
        return $this->belongsTo(JadwalInterview::class, 'jadwal_id');
    }

    public function talent()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function penilai()
    {
        return $this->belongsTo(Admin::class, 'dinilai_oleh');
    }

    public function penyetuju()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    // Scopes
    public function scopeDisetujui($query)
    {
        return $query->whereNotNull('disetujui_oleh');
    }

    public function scopeMenungguPersetujuan($query)
    {
        return $query->whereNull('disetujui_oleh');
    }

    public function scopeByRekomendasi($query, $rekomendasi)
    {
        return $query->where('rekomendasi', $rekomendasi);
    }

    // Mutators & Accessors
    public function getRekomendasiLabelAttribute()
    {
        $labels = [
            'tidak_direkomendasikan' => 'Tidak Direkomendasikan',
            'bersyarat' => 'Bersyarat',
            'direkomendasikan' => 'Direkomendasikan',
            'sangat_direkomendasikan' => 'Sangat Direkomendasikan'
        ];
        
        return $labels[$this->rekomendasi] ?? $this->rekomendasi;
    }

    public function getSkorTotalAttribute()
    {
        $interview = $this->skor_interview ?? 0;
        $psikologi = $this->skor_psikologi ?? 0;
        
        if ($interview > 0 && $psikologi > 0) {
            return ($interview + $psikologi) / 2;
        }
        
        return $interview > 0 ? $interview : $psikologi;
    }
}
