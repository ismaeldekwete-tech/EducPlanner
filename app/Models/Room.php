<?php

namespace App\Models;

class Room extends BaseModel
{
    protected $fillable = ['name', 'capacity', 'is_labo'];

    // Une salle peut avoir plusieurs créneaux d'EDT
    public function entries()
    {
        return $this->hasMany(TimetableEntry::class);
    }

    // Filtre les salles libres sur un jour et créneau donné
    public function scopeAvailableAt($query, string $day, string $slotLabel)
    {
        return $query->whereDoesntHave('entries', function ($q) use ($day, $slotLabel) {
            $q->where('day_of_week', $day)
              ->where('slot_number', $slotLabel);
        });
    }
}
