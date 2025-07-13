<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
class User extends Authenticatable
{
    use HasApiTokens, Notifiable;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'users';
    protected $fillable = [
        'full_name',
        'email',
        'password',
        'phone',
        'national_id',
        'address',
        'driving_license',
        'license_number',
        'role',
        'is_driving_mode',
        'distracting_apps',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_driving_mode' => 'boolean',
        'distracting_apps' => 'array',
    ];
    public function vehicles() {
        return $this->hasMany(Vehicle::class);
    }

    public function violations() {
        return $this->hasMany(Violation::class);
    }
    public function notifications() {
        return $this->hasMany(Notification::class);
    }

    public function emergencyOverrides() {
        return $this->hasMany(EmergencyOverride::class);
    }
}
