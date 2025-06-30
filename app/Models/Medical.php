<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medical extends Model
{
    use HasFactory;

    protected $table = 'medical';

    protected $fillable = [
        'user_id',
        'tanggal',
        'nama',
        'hasil',
        'file',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    protected $appends = [
        'status_label'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    
    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Pending',
            'valid' => 'Valid',
            'tidak_valid' => 'Tidak Valid'
        ];
        
        return $labels[$this->status] ?? $this->status;
    }
}
