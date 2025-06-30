<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interview extends Model
  {
      protected $fillable = [
          'lamaran_id', 'user_id', 'tanggal', 'lokasi', 'pewawancara_id', 'status',
          'skor_interview', 'skor_psikotes', 'kemampuan_komunikasi', 'kemampuan_teknis',
          'kepribadian', 'rekomendasi', 'catatan_jadwal', 'catatan_hasil'
      ];

      protected $casts = [
          'tanggal' => 'date',
          'waktu' => 'datetime'
      ];

      protected $appends = [
        'status_label',
    ];
      public function talent()
      {
          return $this->belongsTo(User::class, 'user_id');
      }


      public function pewawancara()
      {
          return $this->belongsTo(Admin::class, 'pewawancara_id');
      }

      public function lamaran()
      {
          return $this->belongsTo(Lamaran::class, 'lamaran_id');
      }

      // Status helpers
      public function isCompleted() {
          return $this->status === 'selesai';
      }

      public function hasResults() {
          return !is_null($this->skor_interview);
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
  }