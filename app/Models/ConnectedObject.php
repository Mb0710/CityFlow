<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConnectedObject extends Model
{
    protected $fillable =  [
        'unique_id',
        'name',
        'type',
        'description',
        'status',
        'attributes',
        'battery_level',
        'lat',
        'lng',
        'zone_id',
        'last_interaction',


    ];
    //
}
