<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DokumenTalent extends Model
{
    use HasFactory;

    protected $table = 'dokumen_talent';

    protected $fillable = [
        'talent_id',
        'kategori_id',
        'nama_dokumen',
        'path_file',
        'ukuran_file',
        'tipe_file',
        'tanggal_upload',
        'status_verifikasi',
        'diverifikasi_oleh',
        'tanggal_verifikasi',
        'alasan_penolakan',
        'tanggal_kadaluarsa',
    ];

    protected $casts = [
        'tanggal_upload' => 'datetime',
        'tanggal_verifikasi' => 'datetime',
        'tanggal_kadaluarsa' => 'date',
    ];

    // Relationships
    public function talent()
    {
        return $this->belongsTo(Talent::class);
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriDokumen::class, 'kategori_id');
    }

    public function verifikator()
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status_verifikasi', $status);
    }

    public function scopeMenungguVerifikasi($query)
    {
        return $query->where('status_verifikasi', 'menunggu');
    }

    public function scopeDisetujui($query)
    {
        return $query->where('status_verifikasi', 'disetujui');
    }

    public function scopeKadaluarsaBulanIni($query)
    {
        return $query->whereMonth('tanggal_kadaluarsa', now()->month)
                    ->whereYear('tanggal_kadaluarsa', now()->year);
    }

    // Mutators & Accessors
    public function getStatusLabelAttribute()
    {
        $labels = [
            'menunggu' => 'Menunggu Verifikasi',
            'disetujui' => 'Disetujui',
            'ditolak' => 'Ditolak'
        ];
        
        return $labels[$this->status_verifikasi] ?? $this->status_verifikasi;
    }

    public function getUkuranFileFormattedAttribute()
    {
        $bytes = $this->ukuran_file;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getUrlAttribute()
    {
        return Storage::url($this->path_file);
    }
}
