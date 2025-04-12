<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserConnection extends Model
{
    use HasFactory;


    protected $table = 'user_connections';


    protected $fillable = [
        'user_id',
        'connection_time',
        'points_earned'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
