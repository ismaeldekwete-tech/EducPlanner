<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherAvailability extends BaseModel
{
    protected $table = 'teacher_availabilities';

    protected $fillable = [
        'teacher_id',
        'day_of_week',
        'slot_number',
        'is_available',
    ];

    /**
     * Relation avec l'enseignant.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
