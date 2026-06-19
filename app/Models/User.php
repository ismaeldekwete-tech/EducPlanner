<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles; // Pour gérer les rôles Admin/Prof

class User extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'name', 'email', 'password', 'department_id', 'classe_id', 'phone', 'lang',
        'two_factor_secret', 'two_factor_confirmed_at', 'two_factor_type',
        'email_otp_code', 'email_otp_expires_at'
    ];

    // Relation vers la classe de l'étudiant
    public function classe()
    {
        return $this->belongsTo(Classe::class, 'classe_id');
    }

    // Génération UUID à la création
    protected static function boot()
    {
        parent::boot();
        static::creating(fn ($model) => $model->id = (string) Str::uuid());
    }

    // Relation avec le département
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // Relation avec les disponibilités déclarées par le professeur
    public function availabilities()
    {
        return $this->hasMany(TeacherAvailability::class, 'teacher_id');
    }

    // Relation avec les affectations de matières
    public function subjectTeachers()
    {
        return $this->hasMany(SubjectTeacher::class, 'teacher_id');
    }
}
