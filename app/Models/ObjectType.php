<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ObjectType extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'default_unit',
        'description',
        'default_attributes'
    ];

    protected $casts = [
        'default_attributes' => 'array',
    ];

    public function connectedObjects()
    {
        return $this->hasMany(ConnectedObject::class);
    }
}