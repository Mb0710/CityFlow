<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ObjectType extends Model
{
    protected $fillable = [
        'name',
        'description',
        'attributes'
    ];

    protected $casts = [
        'attributes' => 'array',
    ];

    public function connectedObjects()
    {
        return $this->hasMany(ConnectedObject::class, 'type', 'name');
    }
}