<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\VerifyEmail;
use Illuminate\Support\Str;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles, HasApiTokens;
    

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [ 
        'nama', 'email', 'password', 'status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    
    protected $appends = [
        // 'avatar_url',
        'status_label',
        'initial_avatar'
    ];

    public function getAvatarAttribute($value)
    {
        if($this->attributes['avatar']){
            return $this->attributes['avatar'];
        }else{
            return '/media/placeholder/avatar.jpg';
        }
    }

    /**
     * Get all of the posts that are assigned this tag.
     */
    public function detail()
    {
        return $this->hasOne(UserDetail::class, 'user_id');
    }

    public function medical()
    {
        return $this->hasOne(Medical::class, 'user_id');
    }

    
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail); // Use your custom notification
    }

    // Mutators & Accessors
    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Pending',
            'diterima' => 'Diterima',
            'interview' => 'Interview',
            'medical' => 'Medical Check Up',
            'pelatihan' => 'Pelatihan',
            'siap' => 'Siap Berangkat',
            'ditolak' => 'Ditolak',
        ];
        
        return $labels[$this->status] ?? $this->status;
    }

    
    public function getInitialAvatarAttribute()
    {
        // Ambil nama dari atribut name (atau ganti sesuai kolom di database)
        $name = $this->nama;

        if (!$name) {
            return '';
        }

        // Pecah nama berdasarkan spasi dan ambil huruf pertama dari maksimal 2 kata
        $parts = explode(' ', trim($name));
        $initials = strtoupper(
            collect($parts)
                ->take(2)
                ->map(fn($part) => Str::substr($part, 0, 1))
                ->implode('')
        );

        return $initials;
    }
}
