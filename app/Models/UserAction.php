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
}
