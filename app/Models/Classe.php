<?php

namespace App\Models;

class Classe extends BaseModel
{
    protected $fillable = ['filiere', 'regime', 'niveau', 'groupe', 'code_unique', 'department_id', 'room_id'];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // Relation avec la salle attitrée pour la semaine
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    // Une classe peut avoir plusieurs emplois du temps (historique)
    public function timetables()
    {
        return $this->hasMany(Timetable::class);
    }
}
