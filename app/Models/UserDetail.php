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
        'phone',
        'nama_izin',
        'status_izin',
        'nama_ayah',
        'nama_ibu',
        'phone_ortu',
        'alamat_ortu',
        'pendidikan',
        'pengalaman',
        'skill_bahasa',
        'skill_teknis',
        'pekerjaan',
        'negara_tujuan',
        'ktp',
        'kk',
        'akte_lahir',
        'buku_nikah',
        'surat_keterangan_sehat',
        'surat_izin_keluarga',
        'kompetensi',
        'ijazah',
        'paspor',
        'surat_pengalaman_kerja',
        'skck',
        'foto',
        'status',
        'catatan',
        'divalidasi_tanggal',
        'divalidasi_oleh',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'pendidikan' => 'array',
        'pengalaman' => 'array',
        'skill_bahasa' => 'array',
        'skill_teknis' => 'array',
        'divalidasi_tanggal' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'divalidasi_oleh');
    }

    public function getJenisKelaminLabelAttribute()
    {
        return $this->jenis_kelamin === 'Laki-Laki' ? 'Laki-laki' : 'Perempuan';
    }

    public function getUmurAttribute()
    {
        return $this->tanggal_lahir->age ?? 0;
    }
}