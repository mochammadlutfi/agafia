<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DokumenLamaran extends Model
{
    use HasFactory;

    protected $table = 'dokumen_lamaran';

    protected $fillable = [
        'lamaran_id',
        'ktp',
        'kk',
        'akte_lahir',
        'buku_nikah',
        'surat_keterangan_sehat',
        'surat_izin_keluarga',
        'ijazah',
        'sertifikat_keahlian',
        'paspor',
        'surat_pengalaman',
        'skck',
        'status',
        'catatan'
    ];

    protected $appends = [
        'status_label',
        'status_color'
    ];

    // Relationships
    public function lamaran()
    {
        return $this->belongsTo(Lamaran::class, 'lamaran_id');
    }

    
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    
    public function scopeDiterima($query)
    {
        return $query->where('status', 'diterima');
    }

    // Mutators & Accessors
    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Menunggu Review',
            'diterima' => 'Disetujui',
            'ditolak' => 'Ditolak'
        ];

        return $labels[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'pending' => 'warning',
            'diterima' => 'success',
            'ditolak' => 'danger'
        ];

        return $colors[$this->status] ?? 'secondary';
    }
}