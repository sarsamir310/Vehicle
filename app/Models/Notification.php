<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'title', 'message', 'icon', 'is_read'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
