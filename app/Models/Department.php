<?php

namespace App\Models;

class Department extends BaseModel
{
    protected $fillable = ['name', 'code'];

    // Un département a plusieurs classes
    public function classes()
    {
        return $this->hasMany(Classe::class);
    }
}
