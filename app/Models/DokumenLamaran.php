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
        'kategori_dokumen_id',
        'nama_file',
        'file_path',
        'status',
        'catatan',
        'diupload_tanggal',
        'direview_oleh',
        'direview_tanggal',
    ];

    protected $casts = [
        'diupload_tanggal' => 'datetime',
        'direview_tanggal' => 'datetime',
    ];

    protected $appends = [
        'status_label',
        'file_url'
    ];

    // Relationships
    public function lamaran()
    {
        return $this->belongsTo(Lamaran::class, 'lamaran_id');
    }

    public function kategoriDokumen()
    {
        return $this->belongsTo(KategoriDokumen::class, 'kategori_dokumen_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(Admin::class, 'direview_oleh');
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

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeByKategori($query, $kategoriId)
    {
        return $query->where('kategori_dokumen_id', $kategoriId);
    }

    public function scopeByJenisDokumen($query, $jenis)
    {
        return $query->whereHas('kategoriDokumen', function($q) use ($jenis) {
            $q->where('jenis_dokumen', $jenis);
        });
    }

    public function scopePendaftaran($query)
    {
        return $query->byJenisDokumen('pendaftaran');
    }

    public function scopeMedical($query)
    {
        return $query->byJenisDokumen('medical');
    }

    public function scopeKeberangkatan($query)
    {
        return $query->byJenisDokumen('keberangkatan');
    }

    public function scopeWajib($query)
    {
        return $query->whereHas('kategoriDokumen', function($q) {
            $q->where('wajib', true);
        });
    }

    public function scopeDireview($query)
    {
        return $query->whereNotNull('direview_oleh');
    }

    public function scopeBelumDireview($query)
    {
        return $query->whereNull('direview_oleh');
    }

    // Mutators & Accessors
    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Menunggu Review',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak'
        ];

        return $labels[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger'
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    public function getFileUrlAttribute()
    {
        if ($this->file_path) {
            return asset('uploads/' . $this->file_path);
        }
        return null;
    }

    public function getFileExtensionAttribute()
    {
        if ($this->file_path) {
            return pathinfo($this->file_path, PATHINFO_EXTENSION);
        }
        return null;
    }

    public function getFileSizeAttribute()
    {
        if ($this->file_path) {
            $fullPath = public_path('uploads/' . $this->file_path);
            if (file_exists($fullPath)) {
                $bytes = filesize($fullPath);
                $units = ['B', 'KB', 'MB', 'GB'];
                
                for ($i = 0; $bytes > 1024; $i++) {
                    $bytes /= 1024;
                }
                
                return round($bytes, 2) . ' ' . $units[$i];
            }
        }
        return null;
    }

    // Helper methods
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function isWajib()
    {
        return $this->kategoriDokumen && $this->kategoriDokumen->wajib;
    }

    public function isImage()
    {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
        return in_array(strtolower($this->file_extension), $imageExtensions);
    }

    public function isPdf()
    {
        return strtolower($this->file_extension) === 'pdf';
    }

    public function canBeReviewed()
    {
        return $this->status === 'pending';
    }

    public function markAsReviewed($status, $reviewerId, $catatan = null)
    {
        $this->update([
            'status' => $status,
            'direview_oleh' => $reviewerId,
            'direview_tanggal' => now(),
            'catatan' => $catatan
        ]);
    }
}