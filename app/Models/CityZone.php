<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CityZone extends Model
{
    protected $fillable = [
        'name',
        'description',
        'type',
        'lat',
        'lng',
    ];
}
