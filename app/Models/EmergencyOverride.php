<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class EmergencyOverride extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'vehicle_id',
        'category',
        'evidence',
        'status',
        'expires_at',
    ];
    protected $dates = ['expires_at'];
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function vehicle() {
        return $this->belongsTo(Vehicle::class);
    }
    public function violation()
    {
        return $this->hasOne(Violation::class, 'user_id', 'user_id');
    }
    public function canUploadEvidence()
    {
        return Carbon::now()->lessThan($this->expired_at);
    }
}
