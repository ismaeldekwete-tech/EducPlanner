<?php

namespace App\Services;

use App\Models\TimetableEntry;
use App\Models\SubjectTeacher;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Mail;

class TimetableRefusalHandler
{
    protected $generator;

    public function __construct(TimetableGenerator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * Traite le refus d'un enseignant sur un créneau et tente de trouver un remplacement automatique.
     * Retourne le remplacement ou null s'il a été libéré.
     */
    public function handleRefusal(string $entryId, string $reason): ?TimetableEntry
    {
        $entry = TimetableEntry::with(['timetable.classe', 'subjectTeacher.subject', 'subjectTeacher.teacher'])->findOrFail($entryId);
        $timetable = $entry->timetable;
        $classe = $timetable->classe;
        $day = $entry->day_of_week;
        $slotLabel = $entry->slot_number;

        // Journaliser le refus
        AuditLog::log('TEACHER_REFUSAL', [
            'teacher' => $entry->subjectTeacher->teacher->name,
            'subject' => $entry->subjectTeacher->subject->name,
            'classe' => $classe->code_unique,
            'day' => $day,
            'slot' => $slotLabel,
            'reason' => $reason
        ]);

        // 1. Charger les autres affectations de la classe qui ont encore du quota
        $assignments = SubjectTeacher::with(['subject', 'teacher'])
            ->where('classe_id', $classe->id)
            ->where('id', '!=', $entry->subject_teacher_id) // pas la même affectation refusée
            ->get();

        foreach ($assignments as $asm) {
            $subject = $asm->subject;
            $teacher = $asm->teacher;
            $type = $asm->type;

            // a. Vérifier le quota restant
            if ($subject->quota_total_remaining_minutes < 110) {
                continue;
            }

            // b. Vérifier la limite quotidienne (max 2 créneaux par matière par classe par jour)
            $dailyCount = TimetableEntry::where('timetable_id', $timetable->id)
                ->where('day_of_week', $day)
                ->whereHas('subjectTeacher', function ($q) use ($subject) {
                    $q->where('subject_id', $subject->id);
                })->count();

            if ($dailyCount >= 2) {
                continue;
            }

            // c. Vérifier si le prof est disponible généralement
            if (!$this->generator->isTeacherAvailable($teacher->id, $day, $slotLabel)) {
                continue;
            }

            // d. Vérifier si le prof n'est pas déjà occupé ailleurs
            $teacherConflict = TimetableEntry::where('day_of_week', $day)
                ->where('slot_number', $slotLabel)
                ->whereHas('subjectTeacher', function ($q) use ($teacher) {
                    $q->where('teacher_id', $teacher->id);
                })->exists();

            if ($teacherConflict) {
                continue;
            }

            // e. Vérifier si la salle convient
            // Si la matière de remplacement est un TP, la salle actuelle du créneau doit être un labo,
            // ou alors un labo doit être libre
            $currentRoom = $entry->room;
            $suitableRoom = null;

            if ($type === 'TP') {
                if ($currentRoom && $currentRoom->is_labo) {
                    $suitableRoom = $currentRoom;
                } else {
                    $suitableRoom = Room::where('is_labo', true)
                        ->availableAt($day, $slotLabel)
                        ->first();
                }
            } else {
                if ($currentRoom && !$currentRoom->is_labo) {
                    $suitableRoom = $currentRoom;
                } else {
                    $suitableRoom = Room::where('is_labo', false)
                        ->availableAt($day, $slotLabel)
                        ->first();
                }
            }

            if (!$suitableRoom) {
                continue; // Pas de salle appropriée libre pour cette matière alternative
            }

            // Remplacement trouvé !
            $entry->update([
                'subject_teacher_id' => $asm->id,
                'room_id' => $suitableRoom->id,
                'teacher_status' => 'en_attente',
                'rejection_reason' => null
            ]);

            // Logger le remplacement réussi
            AuditLog::log('AUTO_REPLACEMENT_SUCCESS', [
                'classe' => $classe->code_unique,
                'day' => $day,
                'slot' => $slotLabel,
                'replaced_by_subject' => $subject->name,
                'new_teacher' => $teacher->name
            ]);

            // Envoyer un email au nouveau professeur (dans l'app, et notification mail)
            $this->notifyTeacher($entry);

            return $entry;
        }

        // Si aucun remplacement possible, l'entrée n'est plus supprimée, mais conservée avec le statut 'refuse'
        $entry->update([
            'teacher_status' => 'refuse',
            'rejection_reason' => $reason
        ]);

        // Logger la tentative infructueuse de remplacement
        AuditLog::log('AUTO_REPLACEMENT_FAILED_KEEPING_ENTRY', [
            'classe' => $classe->code_unique,
            'day' => $day,
            'slot' => $slotLabel,
            'reason' => $reason
        ]);

        return $entry;
    }

    /**
     * Envoie un e-mail ou une notification système au professeur désigné.
     */
    protected function notifyTeacher(TimetableEntry $entry)
    {
        try {
            $teacher = $entry->subjectTeacher->teacher;
            $subject = $entry->subjectTeacher->subject;
            $classe = $entry->timetable->classe;

            // Envoyer un e-mail via Maildev
            Mail::raw(
                "Bonjour {$teacher->name},\n\n" .
                "Un nouveau cours vous est proposé suite à un réajustement d'emploi du temps :\n" .
                "- Classe : {$classe->code_unique}\n" .
                "- Matière : {$subject->name} ({$entry->subjectTeacher->type})\n" .
                "- Jour : {$entry->day_of_week}\n" .
                "- Créneau : {$entry->slot_number}\n\n" .
                "Merci de vous connecter sur l'application EducPlanner pour confirmer ou refuser votre présence.\n\n" .
                "Cordialement,\nAdministration IUC.",
                function ($message) use ($teacher) {
                    $message->to($teacher->email)
                        ->subject("EducPlanner : Nouvelle proposition de cours");
                }
            );
        } catch (\Exception $e) {
            // Ignorer si la configuration de messagerie n'est pas encore opérationnelle
        }
    }
}
