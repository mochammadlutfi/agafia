<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lowongan extends Model
{
    use HasFactory;

    protected $table = 'lowongan';

    protected $fillable = [
        'perusahaan',
        'posisi',
        'skill',
        'deskripsi',
        'kuota',
        'lokasi',
        'status',
        'foto',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    protected $appends = [
        'status_label',
    ];

    // Relationships
    public function lamaran()
    {
        return $this->hasMany(Lamaran::class, 'lowongan_id');
    }

    // Scopes
    public function scopeBuka($query)
    {
        return $query->where('status', 'buka');
    }

    public function scopeTutup($query)
    {
        return $query->where('status', 'tutup');
    }

    // Mutators & Accessors
    public function getStatusLabelAttribute()
    {
        $labels = [
            'buka' => 'Buka',
            'tutup' => 'Tutup',
        ];
        
        return $labels[$this->status] ?? $this->status;
    }
}
