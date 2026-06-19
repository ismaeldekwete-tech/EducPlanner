<?php

namespace App\Models;

/**
 * @property string $id
 */
class Timetable extends BaseModel
{
    protected $fillable = ['classe_id', 'week_number', 'academic_year', 'status', 'created_by'];

    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }

    public function entries()
    {
        return $this->hasMany(TimetableEntry::class);
    }
}
