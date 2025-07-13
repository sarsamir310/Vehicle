<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{

    use HasFactory;
    protected $fillable = ['user_id','license_plate','vehicle_model'];
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function logs() {
        return $this->hasMany(Log::class);
    }

    public function violations() {
        return $this->hasMany(Violation::class);
    }

    public function driverMonitoring() {
        return $this->hasMany(DriverMonitoring::class);
    }
}
