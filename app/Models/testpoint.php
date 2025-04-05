<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestPoint extends Model
{
    protected $table = 'pointtest';
    protected $fillable = ['name', 'lat', 'lng'];
}
?>