<?php

namespace App\Livewire;

use App\Models\Classe;
use App\Models\Timetable;
use Livewire\Component;

class StudentDashboard extends Component
{
    public string $selectedClasseId = '';

    public ?Timetable $publishedTimetable = null;

    public array $entries = [];

    public function mount()
    {
        if (! auth()->user() || ! auth()->user()->hasRole('Etudiant')) {
            abort(403, 'Accès réservé aux étudiants.');
        }

        $studentClassId = auth()->user()->classe_id;
        if ($studentClassId) {
            $this->selectedClasseId = $studentClassId;
            $this->loadTimetable();
            return;
        }

        $firstClass = Classe::first();
        if ($firstClass) {
            $this->selectedClasseId = $firstClass->id;
            $this->loadTimetable();
        }
    }

    public function updatedSelectedClasseId()
    {
        $this->loadTimetable();
    }

    public function loadTimetable()
    {
        if (! $this->selectedClasseId) {
            $this->entries = [];
            $this->publishedTimetable = null;

            return;
        }

        $this->publishedTimetable = Timetable::with(['classe', 'entries.subjectTeacher.subject', 'entries.subjectTeacher.teacher', 'entries.room'])
            ->where('classe_id', $this->selectedClasseId)
            ->where('status', 'publie')
            ->where('week_number', (int) date('W'))
            ->where('academic_year', date('Y'))
            ->first();

        if (! $this->publishedTimetable) {
            $this->entries = [];

            return;
        }

        $this->publishedTimetable->entries->loadMissing(['subjectTeacher.subject', 'subjectTeacher.teacher', 'room']);

        $this->entries = $this->publishedTimetable->entries->map(function ($entry) {
            $subjectTeacher = $entry->subjectTeacher;

            return [
                'day_of_week' => $entry->day_of_week,
                'slot_number' => $entry->slot_number,
                'subject_teacher' => [
                    'type' => $subjectTeacher?->type ?? 'CM',
                    'subject' => $subjectTeacher?->subject?->toArray(),
                    'teacher' => $subjectTeacher?->teacher?->toArray(),
                ],
                'room' => $entry->room?->toArray(),
            ];
        })->toArray();
    }

    public function render()
    {
        $classes = Classe::all();
        $days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
        $slots = [
            ['label' => '08:00 - 09:50'],
            ['label' => '10:10 - 12:00'],
            ['label' => '13:00 - 14:50'],
            ['label' => '15:10 - 17:00'],
            ['label' => '17:30 - 19:30'],
            ['label' => '20:00 - 21:30'],
        ];

        return view('livewire.student-dashboard', [
            'classes' => $classes,
            'days' => $days,
            'slots' => $slots,
        ])->layout('layouts.app');
    }
}
