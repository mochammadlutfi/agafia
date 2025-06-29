<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    use HasFactory;

    protected $table = 'training';

    protected $fillable = [
        'user_id',
        'program_id',
        'tanggal_daftar',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'nilai_akhir',
        'sertifikat_diterbitkan',
        'nomor_sertifikat',
        'catatan',
        'didaftarkan_oleh',
    ];

    protected $casts = [
        'tanggal_daftar' => 'date',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'sertifikat_diterbitkan' => 'boolean',
    ];

    protected $appends = [
        'status_label'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function program()
    {
        return $this->belongsTo(ProgramTraining::class, 'program_id');
    }

    public function staff()
    {
        return $this->belongsTo(Admin::class, 'didaftarkan_oleh');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeSelesai($query)
    {
        return $query->where('status', 'selesai');
    }

    public function scopeBerlangsung($query)
    {
        return $query->whereIn('status', ['terdaftar', 'sedang_pelatihan']);
    }

    public function scopeBerhasil($query)
    {
        return $query->where('status', 'selesai')
                    ->where('nilai_akhir', '>=', 75);
    }

    // Mutators & Accessors
    public function getStatusLabelAttribute()
    {
        $labels = [
            'terdaftar' => 'Terdaftar',
            'sedang_pelatihan' => 'Sedang Pelatihan',
            'selesai' => 'Selesai',
            'mengundurkan_diri' => 'Mengundurkan Diri'
        ];
        
        return $labels[$this->status] ?? $this->status;
    }

    public function getGradeAttribute()
    {
        if (!$this->nilai_akhir) return null;
        
        if ($this->nilai_akhir >= 90) return 'A';
        if ($this->nilai_akhir >= 80) return 'B';
        if ($this->nilai_akhir >= 70) return 'C';
        if ($this->nilai_akhir >= 60) return 'D';
        return 'E';
    }

    public function getLulusAttribute()
    {
        return $this->nilai_akhir >= 75;
    }
}
