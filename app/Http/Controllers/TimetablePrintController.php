<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Timetable;
use App\Models\TimetableEntry;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class TimetablePrintController extends Controller
{
    /**
     * Print the officially published weekly timetable for a student's class.
     */
    public function studentPrint(string $classeId)
    {
        $classe = Classe::findOrFail($classeId);
        
        $user = auth()->user();
        $isAdmin = $user && $user->hasAnyRole(['SuperAdmin', 'ChefDepartement']);

        // Find the weekly timetable for this week, or fallback to the latest
        $query = Timetable::where('classe_id', $classeId);
        
        if (!$isAdmin) {
            $query->where('status', 'publie');
        }

        $timetable = (clone $query)->where('week_number', (int) date('W'))
            ->where('academic_year', date('Y'))
            ->first();

        if (!$timetable) {
            $timetable = $query->orderBy('week_number', 'desc')->first();
        }

        if (!$timetable) {
            abort(404, $isAdmin ? __('Aucun emploi du temps disponible pour cette classe.') : __('Aucun emploi du temps publié disponible pour cette classe.'));
        }

        $entries = $timetable->entries()->with(['subjectTeacher.subject', 'subjectTeacher.teacher', 'room'])->get();

        $pdf = Pdf::loadView('timetable.print', [
            'type' => 'student',
            'classe' => $classe,
            'timetable' => $timetable,
            'entries' => $entries,
            'days' => ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
            'slots' => [
                ['label' => '08:00 - 09:50'],
                ['label' => '10:10 - 12:00'],
                ['label' => '13:00 - 14:50'],
                ['label' => '15:10 - 17:00'],
                ['label' => '17:30 - 19:30'],
                ['label' => '20:00 - 21:30'],
            ],
            'week' => $timetable->week_number,
            'academicYear' => $timetable->academic_year,
        ]);

        return $pdf->stream('EDT_' . $classe->code_unique . '_Semaine_' . $timetable->week_number . '.pdf');
    }

    /**
     * Print the weekly planning for the currently authenticated teacher.
     */
    public function teacherPrint()
    {
        $teacher = auth()->user();
        if (!$teacher || !$teacher->hasRole('Professeur')) {
            abort(403, __('Action non autorisée.'));
        }

        $targetWeek = request('week', 'current');
        
        if ($targetWeek === 'next') {
            $nextWeekTime = strtotime('+1 week');
            $week = (int) date('W', $nextWeekTime);
            $academicYear = date('Y', $nextWeekTime);

            // Fetch teacher's entries for next week (ONLY officially published)
            $entries = TimetableEntry::with(['subjectTeacher.subject', 'timetable.classe', 'room'])
                ->whereHas('subjectTeacher', function ($q) use ($teacher) {
                    $q->where('teacher_id', $teacher->id);
                })
                ->whereHas('timetable', function ($q) use ($week, $academicYear) {
                    $q->where('week_number', $week)
                      ->where('academic_year', $academicYear)
                      ->where('status', 'publie'); // Restrict to officially published
                })
                ->where('teacher_status', '!=', 'refuse')
                ->get();
        } else {
            $week = (int) date('W');
            $academicYear = date('Y');

            // Fetch teacher's entries for the current week
            $entries = TimetableEntry::with(['subjectTeacher.subject', 'timetable.classe', 'room'])
                ->whereHas('subjectTeacher', function ($q) use ($teacher) {
                    $q->where('teacher_id', $teacher->id);
                })
                ->whereHas('timetable', function ($q) use ($week, $academicYear) {
                    $q->where('week_number', $week)
                      ->where('academic_year', $academicYear);
                })
                ->where('teacher_status', '!=', 'refuse')
                ->get();

            // Fallback to the latest week with scheduled entries if current week has nothing
            if ($entries->isEmpty()) {
                $latestEntry = TimetableEntry::whereHas('subjectTeacher', function ($q) use ($teacher) {
                    $q->where('teacher_id', $teacher->id);
                })->where('teacher_status', '!=', 'refuse')
                  ->latest()
                  ->first();

                if ($latestEntry && $latestEntry->timetable) {
                    $week = $latestEntry->timetable->week_number;
                    $academicYear = $latestEntry->timetable->academic_year;
                    $entries = TimetableEntry::with(['subjectTeacher.subject', 'timetable.classe', 'room'])
                        ->whereHas('subjectTeacher', function ($q) use ($teacher) {
                            $q->where('teacher_id', $teacher->id);
                        })
                        ->whereHas('timetable', function ($q) use ($week, $academicYear) {
                            $q->where('week_number', $week)
                              ->where('academic_year', $academicYear);
                        })
                        ->where('teacher_status', '!=', 'refuse')
                        ->get();
                }
            }
        }

        $pdf = Pdf::loadView('timetable.print', [
            'type' => 'teacher',
            'teacher' => $teacher,
            'entries' => $entries,
            'days' => ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
            'slots' => [
                ['label' => '08:00 - 09:50'],
                ['label' => '10:10 - 12:00'],
                ['label' => '13:00 - 14:50'],
                ['label' => '15:10 - 17:00'],
                ['label' => '17:30 - 19:30'],
                ['label' => '20:00 - 21:30'],
            ],
            'week' => $week,
            'academicYear' => $academicYear,
        ]);

        $teacherNameClean = str_replace(' ', '_', $teacher->name);
        return $pdf->stream('EDT_Enseignant_' . $teacherNameClean . '_Semaine_' . $week . '.pdf');
    }

    /**
     * Print any weekly timetable draft/published for admins.
     */
    public function adminPrint(string $timetableId)
    {
        $user = auth()->user();
        if (!$user || !$user->hasAnyRole(['SuperAdmin', 'ChefDepartement'])) {
            abort(403, __('Action non autorisée.'));
        }

        $timetable = Timetable::with('classe')->findOrFail($timetableId);
        $entries = $timetable->entries()->with(['subjectTeacher.subject', 'subjectTeacher.teacher', 'room'])->get();

        $pdf = Pdf::loadView('timetable.print', [
            'type' => 'student',
            'classe' => $timetable->classe,
            'timetable' => $timetable,
            'entries' => $entries,
            'days' => ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
            'slots' => [
                ['label' => '08:00 - 09:50'],
                ['label' => '10:10 - 12:00'],
                ['label' => '13:00 - 14:50'],
                ['label' => '15:10 - 17:00'],
                ['label' => '17:30 - 19:30'],
                ['label' => '20:00 - 21:30'],
            ],
            'week' => $timetable->week_number,
            'academicYear' => $timetable->academic_year,
        ]);

        return $pdf->stream('EDT_ADMIN_' . $timetable->classe->code_unique . '_Semaine_' . $timetable->week_number . '.pdf');
    }
}
