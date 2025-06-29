<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriDokumen extends Model
{
    use HasFactory;

    protected $table = 'kategori_dokumen';

    protected $fillable = [
        'nama_kategori',
        'deskripsi',
        'wajib',
        'jenis_dokumen',
    ];

    protected $casts = [
        'wajib' => 'boolean',
    ];

    protected $appends = [
        'jenis_dokumen_label',
    ];

    // Scopes
    public function scopeWajib($query)
    {
        return $query->where('wajib', true);
    }

    public function scopeByJenis($query, $jenis)
    {
        return $query->where('jenis_dokumen', $jenis);
    }

    public function scopeMedical($query)
    {
        return $query->where('jenis_dokumen', 'medical');
    }

    public function scopeKeberangkatan($query)
    {
        return $query->where('jenis_dokumen', 'keberangkatan');
    }

    public function scopeUmum($query)
    {
        return $query->where('jenis_dokumen', 'umum');
    }

    // Mutators & Accessors
    public function getJenisDokumenLabelAttribute()
    {
        $labels = [
            'medical' => 'Medical',
            'keberangkatan' => 'Keberangkatan',
            'umum' => 'Umum',
        ];
        
        return $labels[$this->jenis_dokumen] ?? $this->jenis_dokumen;
    }
}