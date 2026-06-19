<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Classe parente pour forcer l'utilisation des UUID
 */
abstract class BaseModel extends Model
{
    // Indique à Laravel que la clé primaire n'est pas un incrément entier
    public $incrementing = false;

    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        // Génère un UUID automatiquement à la création d'un enregistrement
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }
}
