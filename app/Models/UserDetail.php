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
        'nama_lengkap',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'status_pernikahan',
        'alamat',
        'nomor_telepon',
        'nama_kontak_darurat',
        'telepon_kontak_darurat',
        'tingkat_pendidikan',
        'keahlian',
        'pengalaman_kerja',
        'kemampuan_bahasa',
        'foto',
        'status_pendaftaran',
        'catatan',
        'divalidasi_tanggal',
        'divalidasi_oleh',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        // 'pendidikan' => 'array',
        // 'pengalaman' => 'array',
        'kemampuan_bahasa' => 'array',
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

    public function jadwalInterview()
    {
        return $this->hasMany(JadwalInterview::class);
    }

    public function hasilInterview()
    {
        return $this->hasMany(HasilInterview::class);
    }

    public function dokumen()
    {
        return $this->hasMany(DokumenTalent::class);
    }

    public function pendaftaranPelatihan()
    {
        return $this->hasMany(PendaftaranPelatihan::class);
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status_pendaftaran', $status);
    }

    public function scopeByJenisKelamin($query, $jenisKelamin)
    {
        return $query->where('jenis_kelamin', $jenisKelamin);
    }

    public function scopeTervalidasi($query)
    {
        return $query->whereIn('status_pendaftaran', ['tervalidasi', 'sudah_interview', 'medical', 'pelatihan', 'siap']);
    }

    public function getJenisKelaminLabelAttribute()
    {
        return $this->jenis_kelamin === 'laki_laki' ? 'Laki-laki' : 'Perempuan';
    }

    public function getUmurAttribute()
    {
        return $this->tanggal_lahir->age ?? 0;
    }

    public function getPendidikanAttribute($value)
    {
        return json_decode($value, true);
    }
    public function getPengalamanAttribute($value)
    {
        return json_decode($value, true);
    }
    // public function getPendidikanAttribute()
    // {
    //     return $this->pendidikan ? json_decode($this->pendidikan) : [];
    // }
    // public function getPengalamanAttribute()
    // {
    //     return json_decode($this->pengalaman) ?? [];
    // }
}
