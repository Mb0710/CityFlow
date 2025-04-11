<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAction extends Model
{
    protected $fillable = [
        'user_id',
        'action_type',
        'object_id',
        'description',
        'points'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function connectedObject()
    {
        return $this->belongsTo(ConnectedObject::class, 'object_id');
    }
}
