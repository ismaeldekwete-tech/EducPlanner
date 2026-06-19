<?php

namespace App\Models;

class Subject extends BaseModel
{
    protected $fillable = [
        'name', 'code', 'semester',
        'quota_cm_minutes', 'quota_td_minutes', 'quota_tp_minutes',
        'quota_total_remaining_minutes', 'department_id',
    ];

    // Relation avec les profs assignés
    public function teachers()
    {
        // Remplacez 'user_id' par le vrai nom de la colonne dans votre table (probablement 'teacher_id')
        return $this->belongsToMany(User::class, 'subject_teacher', 'subject_id', 'teacher_id')
            ->withPivot('id', 'type', 'classe_id');
    }
}
