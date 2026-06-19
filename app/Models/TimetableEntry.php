<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimetableEntry extends BaseModel
{
    protected $guarded = [];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function timetable()
    {
        return $this->belongsTo(Timetable::class);
    }

    /**
     * Relation vers la table de liaison (pivot) subject_teacher.
     * Note : Assurez-vous que votre modèle pivot existe ou que le nom de la table est correct.
     */
    public function subjectTeacher(): BelongsTo
    {
        // Si vous avez un modèle dédié, utilisez-le.
        // Sinon, Laravel traitera 'subject_teacher' comme une table.
        return $this->belongsTo(SubjectTeacher::class, 'subject_teacher_id');
    }
}
