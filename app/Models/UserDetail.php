<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    use HasFactory;

    protected $table = 'user_details';
    
    protected $fillable = [
        'user_id',
        'nik',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'alamat_domisili',
        'kode_pos',
        'kecamatan',
        'kabupaten_kota',
        'provinsi',
        'phone',
        'kontak_darurat_nama',
        'kontak_darurat_phone',
        'kontak_darurat_hubungan',
        'kontak_darurat_alamat',
        'pendidikan',
        'pengalaman',
        'pengalaman_luar_negeri',
        'skill_bahasa',
        'bahasa_asing_skor',
        'visa_history',
        'skill_teknis',
        'keahlian_khusus',
        'objektif_karir',
        'ringkasan_profil',
        'hobi',
        'motto_hidup',
        'sertifikat',
        'prestasi',
        'achievement_highlights',
        'organisasi',
        'referensi',
        'pekerjaan',
        'negara_tujuan',
        'foto',
        'agama',
        'status_perkawinan',
        'tinggi_badan',
        'berat_badan',
        'golongan_darah',
        'kondisi_kesehatan',
        'medical_checkup_terakhir',
        'email_alternatif',
        'media_sosial',
        'catatan',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'pendidikan' => 'array',
        'pengalaman' => 'array',
        'pengalaman_luar_negeri' => 'array',
        'skill_bahasa' => 'array',
        'bahasa_asing_skor' => 'array',
        'visa_history' => 'array',
        'skill_teknis' => 'array',
        'sertifikat' => 'array',
        'prestasi' => 'array',
        'organisasi' => 'array',
        'referensi' => 'array',
        'media_sosial' => 'array',
        'medical_checkup_terakhir' => 'date',
        'cv_generated_at' => 'datetime',
        'divalidasi_tanggal' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function getJenisKelaminLabelAttribute()
    {
        return $this->jenis_kelamin === 'Laki-Laki' ? 'Laki-laki' : 'Perempuan';
    }

    public function getUmurAttribute()
    {
        return $this->tanggal_lahir->age ?? 0;
    }

    public function getAlamatLengkapAttribute()
    {
        $alamat = $this->alamat_domisili ?: $this->alamat;
        $parts = array_filter([
            $alamat,
            $this->kecamatan,
            $this->kabupaten_kota,
            $this->provinsi,
            $this->kode_pos
        ]);
        return implode(', ', $parts);
    }

    public function getPengalamanKerjaAttribute()
    {
        $pengalaman = collect($this->pengalaman ?? []);
        $pengalamanLN = collect($this->pengalaman_luar_negeri ?? []);
        
        return $pengalaman->merge($pengalamanLN)->sortByDesc(function($item) {
            return $item['tahun_selesai'] ?? $item['tahun_mulai'] ?? 0;
        })->values()->all();
    }

    public function getSertifikatAktifAttribute()
    {
        return collect($this->sertifikat ?? [])->filter(function($cert) {
            if (!isset($cert['tanggal_expired'])) return true;
            return now()->lessThan($cert['tanggal_expired']);
        })->values()->all();
    }

    public function getKompetensiUtamaAttribute()
    {
        $skills = collect($this->skill_teknis ?? []);
        $keahlian = $this->keahlian_khusus ? explode(',', $this->keahlian_khusus) : [];
        
        return $skills->merge($keahlian)->filter()->unique()->values()->all();
    }

    public function isCvDataComplete()
    {
        $requiredFields = [
            'nama', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin',
            'alamat', 'phone', 'pendidikan', 'pengalaman'
        ];
        
        foreach($requiredFields as $field) {
            if (empty($this->$field)) return false;
        }
        
        return true;
    }
}