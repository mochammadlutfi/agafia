<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lamaran extends Model
{
    use HasFactory;

    protected $table = 'lamaran';

    protected $fillable = [
        'user_id',
        'lowongan_id',
        'tanggal_lamaran',
        'status',
        'cv_file',
        'catatan',
    ];

    protected $casts = [
        'tanggal_lamaran' => 'date',
    ];

    protected $appends = [
        'status_label'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function lowongan()
    {
        return $this->belongsTo(Lowongan::class, 'lowongan_id');
    }

    public function dokumen()
    {
        return $this->hasMany(DokumenLamaran::class, 'lamaran_id');
    }

    public function interview()
    {
        return $this->hasMany(Interview::class, 'lamaran_id');
    }

    public function hasilInterview()
    {
        return $this->hasMany(HasilInterview::class, 'lamaran_id');
    }

    public function training()
    {
        return $this->hasMany(Training::class, 'lamaran_id');
    }


    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeDiterima($query)
    {
        return $query->where('status', 'diterima');
    }

    public function scopeDitolak($query)
    {
        return $query->where('status', 'ditolak');
    }

    public function scopeInterview($query)
    {
        return $query->where('status', 'interview');
    }

    public function scopeMedical($query)
    {
        return $query->where('status', 'medical');
    }

    public function scopePelatihan($query)
    {
        return $query->where('status', 'pelatihan');
    }

    public function scopeSiap($query)
    {
        return $query->where('status', 'siap');
    }

    public function scopeSelesai($query)
    {
        return $query->where('status', 'selesai');
    }

    // Mutators & Accessors
    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Menunggu Review',
            'diterima' => 'Diterima',
            'ditolak' => 'Ditolak',
            'interview' => 'Tahap Interview',
            'medical' => 'Medical Check Up',
            'pelatihan' => 'Pelatihan',
            'siap' => 'Siap Berangkat',
            'selesai' => 'Selesai'
        ];

        return $labels[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'pending' => 'warning',
            'diterima' => 'success',
            'ditolak' => 'danger',
            'interview' => 'info',
            'medical' => 'primary',
            'pelatihan' => 'purple',
            'siap' => 'success',
            'selesai' => 'success'
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    // Helper methods
    public function isActive()
    {
        return !in_array($this->status, ['ditolak', 'selesai']);
    }

    public function canBeUpdated()
    {
        return in_array($this->status, ['pending', 'diterima', 'interview', 'medical', 'pelatihan']);
    }

    public function getProgressPercentage()
    {
        $statusProgress = [
            'pending' => 10,
            'diterima' => 25,
            'interview' => 40,
            'medical' => 60,
            'pelatihan' => 80,
            'siap' => 90,
            'selesai' => 100,
            'ditolak' => 0
        ];

        return $statusProgress[$this->status] ?? 0;
    }
}