<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalInterview extends Model
{
    use HasFactory;


    protected $table = 'jadwal_interview';

    protected $fillable = [
        'user_id',
        'tanggal',
        'waktu',
        'jenis_interview',
        'lokasi',
        'pewawancara',
        'catatan',
        'status',
        'dibuat_oleh',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'waktu' => 'datetime',
    ];

    protected $appends = [
        'status_label',
        'jenis_label',
    ];

    // Relationships
    public function talent()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pewawancara()
    {
        return $this->belongsTo(Admin::class, 'pewawancara_id');
    }

    public function pembuat()
    {
        return $this->belongsTo(Admin::class, 'dibuat_oleh');
    }

    public function hasil()
    {
        return $this->hasOne(HasilInterview::class, 'jadwal_id');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeHariIni($query)
    {
        return $query->whereDate('tanggal', today());
    }

    public function scopeMingguIni($query)
    {
        return $query->whereBetween('tanggal', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    // Mutators & Accessors
    public function getStatusLabelAttribute()
    {
        $labels = [
            'dijadwalkan' => 'Dijadwalkan',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
            'dijadwal_ulang' => 'Dijadwal Ulang'
        ];
        
        return $labels[$this->status] ?? $this->status;
    }

    public function getJenisLabelAttribute()
    {
        $labels = [
            'interview' => 'Interview',
            'test_psikologi' => 'Test Psikologi',
            'keduanya' => 'Interview & Test Psikologi'
        ];
        
        return $labels[$this->jenis_interview] ?? $this->jenis_interview;
    }
}
