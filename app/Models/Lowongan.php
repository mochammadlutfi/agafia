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

    // Scopes
    public function scopeBuka($query)
    {
        return $query->where('status', 'buka');
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
