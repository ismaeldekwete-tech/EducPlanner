<?php

namespace App\Services;

use App\Models\Classe;
use App\Models\Room;
use App\Models\Subject;
use App\Models\SubjectTeacher;
use App\Models\TeacherAvailability;
use App\Models\Timetable;
use App\Models\TimetableEntry;
use App\Models\AuditLog;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class TimetableGenerator
{
    // Tranches horaires fixes officielles de l'IUC
    public const SLOTS = [
        1 => ['label' => '08:00 - 09:50', 'start' => '08:00', 'end' => '09:50'],
        2 => ['label' => '10:10 - 12:00', 'start' => '10:10', 'end' => '12:00'],
        3 => ['label' => '13:00 - 14:50', 'start' => '13:00', 'end' => '14:50'],
        4 => ['label' => '15:10 - 17:00', 'start' => '15:10', 'end' => '17:00'],
        5 => ['label' => '17:30 - 19:30', 'start' => '17:30', 'end' => '19:30'],
        6 => ['label' => '20:00 - 21:30', 'start' => '20:00', 'end' => '21:30'],
    ];

    public const DAYS = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'];

    /**
     * Génère l'emploi du temps pour une classe donnée sur la semaine courante.
     */
    public function generateForClasse(string $classeId, ?int $weekNumber = null, ?string $academicYear = null): Timetable
    {
        $classe = Classe::findOrFail($classeId);
        $weekNumber = $weekNumber ?? (int) date('W');
        $academicYear = $academicYear ?? date('Y');

        return DB::transaction(function () use ($classe, $weekNumber, $academicYear) {
            // 1. Rechercher ou créer l'emploi du temps de la semaine au statut 'brouillon'
            $timetable = Timetable::firstOrCreate([
                'classe_id' => $classe->id,
                'week_number' => $weekNumber,
                'academic_year' => $academicYear,
            ], [
                'status' => 'brouillon',
                'created_by' => auth()->id() ?? \App\Models\User::role('SuperAdmin')->first()->id ?? \App\Models\User::first()->id ?? null,
            ]);

            // Si déjà publié, on ne touche à rien
            if ($timetable->status === 'publie') {
                throw new \Exception("Cet emploi du temps est déjà publié et ne peut pas être régénéré.");
            }

            // 2. Nettoyer les entrées existantes du brouillon avant régénération
            TimetableEntry::where('timetable_id', $timetable->id)->delete();

            // 3. Charger les affectations de matières de la classe
            $assignments = SubjectTeacher::with(['subject', 'teacher'])
                ->where('classe_id', $classe->id)
                ->get();

            // 4. Déterminer les quotas de temps restants par matière (en minutes)
            $tempQuotas = [];
            foreach ($assignments as $asm) {
                if (!isset($tempQuotas[$asm->subject_id])) {
                    $tempQuotas[$asm->subject_id] = $asm->subject->quota_total_remaining_minutes;
                }
            }

            // --- PASSAGE 1 : PLANIFICATION CONTIGUË STRICTE ET PAR BLOC DE MATIÈRES ---
            // Pour chaque jour de la semaine
            foreach (self::DAYS as $day) {
                // Déterminer les créneaux autorisés triés pour ce jour
                $allowedSlotKeys = [];
                foreach (self::SLOTS as $slotKey => $slotData) {
                    if ($this->isSlotAllowedForRegime($classe->regime, $day, $slotKey)) {
                        $allowedSlotKeys[] = $slotKey;
                    }
                }
                sort($allowedSlotKeys);

                if (empty($allowedSlotKeys)) {
                    continue;
                }

                // Trouver la meilleure configuration contiguë sans trous pour cette journée
                $bestSchedule = $this->findBestContiguousScheduleForDay($day, $tempQuotas, $timetable, $assignments, $allowedSlotKeys);

                if (!empty($bestSchedule)) {
                    foreach ($bestSchedule as $slotLabel => $asm) {
                        TimetableEntry::create([
                            'id' => (string) Str::uuid(),
                            'timetable_id' => $timetable->id,
                            'subject_teacher_id' => $asm->id,
                            'room_id' => $asm->selected_room_id,
                            'day_of_week' => $day,
                            'slot_number' => $slotLabel,
                            'teacher_status' => 'en_attente',
                        ]);
                        $tempQuotas[$asm->subject_id] -= 110;
                    }
                }
            }

            // --- PASSAGE 2 : RATTRAPAGE ---
            // S'il reste du quota non planifié, on tente de le placer.
            $hasRemainingQuota = false;
            foreach ($tempQuotas as $quota) {
                if ($quota >= 110) {
                    $hasRemainingQuota = true;
                    break;
                }
            }

            if ($hasRemainingQuota) {
                // ÉTAPE 2A : EXPANSION CONTIGUË (SANS CRÉATION DE TROUS)
                $tempQuotas = $this->runPass2A($timetable, $classe, $assignments, $tempQuotas);

                // ÉTAPE 2B : DERNIER RECOURS (MESURE EXTRÊME - AVEC AUTORISATION DE TROUS)
                $tempQuotas = $this->runPass2B($timetable, $classe, $assignments, $tempQuotas);
            }

            // Logger l'action d'audit
            AuditLog::log('GENERATE_TIMETABLE', [
                'classe' => $classe->code_unique,
                'week' => $weekNumber,
                'year' => $academicYear,
                'entries_count' => $timetable->entries()->count()
            ]);

            return $timetable;
        });
    }

    /**
     * Recherche la meilleure configuration de cours contiguë pour une journée.
     */
    private function findBestContiguousScheduleForDay(string $day, array $quotas, Timetable $timetable, $assignments, array $allowedSlotKeys): array
    {
        $bestScore = -1;
        $bestSchedule = [];
        $bestSubsegment = [];

        // Générer tous les sous-segments contigus de créneaux autorisés
        $subsegments = $this->getContiguousSubsegments($allowedSlotKeys);

        foreach ($subsegments as $subsegment) {
            if (empty($subsegment)) {
                continue;
            }

            $slots = [];
            foreach ($subsegment as $slotKey) {
                $slots[] = self::SLOTS[$slotKey]['label'];
            }

            // Recherche par backtracking
            $schedule = $this->searchAssignmentsForSlots($day, $slots, 0, $quotas, [], null, $timetable, $assignments);
            if ($schedule !== null) {
                $score = $this->scoreSchedule($schedule);
                if ($score > $bestScore) {
                    $bestScore = $score;
                    $bestSchedule = $schedule;
                    $bestSubsegment = $subsegment;
                }
            }
        }

        // Convertir le tableau indexé en [slot_label => assignment]
        $result = [];
        if (!empty($bestSchedule) && !empty($bestSubsegment)) {
            foreach ($bestSchedule as $index => $asm) {
                $slotLabel = self::SLOTS[$bestSubsegment[$index]]['label'];
                $result[$slotLabel] = $asm;
            }
        }

        return $result;
    }

    /**
     * Algorithme récursif de recherche par Backtracking pour attribuer des matières à une liste de créneaux.
     */
    private function searchAssignmentsForSlots(
        string $day,
        array $slots,
        int $slotIndex,
        array $quotas,
        array $assigned,
        ?string $prevSubjectName,
        Timetable $timetable,
        $assignments
    ): ?array {
        if ($slotIndex === count($slots)) {
            return $assigned;
        }

        $slotLabel = $slots[$slotIndex];

        // Filtrer les affectations ayant encore du quota
        $eligibleAsms = $assignments->filter(function ($asm) use ($quotas) {
            return ($quotas[$asm->subject_id] ?? 0) >= 110;
        });

        // RÈGLE DE COHÉSION DE BLOC :
        // Si le créneau précédent était assigné à un sujet S, et qu'il reste du quota pour S,
        // et que le professeur de S est disponible, on doit obligatoirement continuer avec S.
        $mustScheduleSameSubject = false;
        if ($prevSubjectName !== null && count($assigned) > 0) {
            $prevAsm = $assigned[count($assigned) - 1];
            $prevSubjectId = $prevAsm->subject_id;

            if (($quotas[$prevSubjectId] ?? 0) >= 110) {
                $sameNameAsms = $eligibleAsms->filter(function ($asm) use ($prevSubjectName) {
                    return $asm->subject->name === $prevSubjectName;
                });

                foreach ($sameNameAsms as $asm) {
                    if ($this->isTeacherAvailable($asm->teacher_id, $day, $slotLabel) &&
                        !$this->hasTeacherConflict($asm->teacher_id, $day, $slotLabel, $timetable->id) &&
                        $this->findSuitableFreeRoom($timetable->classe, $asm->type, $day, $slotLabel)) {
                        $mustScheduleSameSubject = true;
                        break;
                    }
                }
            }
        }

        $asmsToTry = $eligibleAsms;
        if ($mustScheduleSameSubject) {
            $asmsToTry = $eligibleAsms->filter(function ($asm) use ($prevSubjectName) {
                return $asm->subject->name === $prevSubjectName;
            });
        } else {
            $asmsToTry = $asmsToTry->shuffle();
        }

        foreach ($asmsToTry as $asm) {
            // 1. Disponibilité enseignant
            if (!$this->isTeacherAvailable($asm->teacher_id, $day, $slotLabel)) {
                continue;
            }

            // 2. Conflit enseignant
            if ($this->hasTeacherConflict($asm->teacher_id, $day, $slotLabel, $timetable->id)) {
                continue;
            }

            // 3. Salle libre
            $room = $this->findSuitableFreeRoom($timetable->classe, $asm->type, $day, $slotLabel);
            if (!$room) {
                continue;
            }

            // 4. Limite journalière : Max 2 créneaux de cette matière par classe par jour
            $dailyCount = 0;
            foreach ($assigned as $prevAsm) {
                if ($prevAsm->subject_id === $asm->subject_id) {
                    $dailyCount++;
                }
            }
            if ($dailyCount >= 2) {
                continue;
            }

            // Cloner l'affectation pour attacher la salle sélectionnée sans affecter les autres branches
            $asmWithRoom = clone $asm;
            $asmWithRoom->selected_room_id = $room->id;

            $nextQuotas = $quotas;
            $nextQuotas[$asm->subject_id] -= 110;

            $nextAssigned = $assigned;
            $nextAssigned[] = $asmWithRoom;

            $result = $this->searchAssignmentsForSlots(
                $day,
                $slots,
                $slotIndex + 1,
                $nextQuotas,
                $nextAssigned,
                $asm->subject->name,
                $timetable,
                $assignments
            );

            if ($result !== null) {
                return $result;
            }
        }

        return null;
    }

    /**
     * Évalue la qualité d'une configuration journalière.
     * Favorise la densité et le regroupement consécutif (back-to-back).
     */
    private function scoreSchedule(array $schedule): int
    {
        $score = count($schedule) * 10;
        $assignments = array_values($schedule);
        $n = count($assignments);

        for ($i = 0; $i < $n - 1; $i++) {
            if ($assignments[$i]->subject->name === $assignments[$i+1]->subject->name) {
                $score += 5; // Bonus pour le regroupement back-to-back
            }
        }

        return $score;
    }

    /**
     * Génère tous les sous-segments contigus possibles d'un tableau d'indices.
     */
    private function getContiguousSubsegments(array $arr): array
    {
        $subsegments = [];
        $n = count($arr);
        for ($len = $n; $len >= 1; $len--) {
            for ($i = 0; $i <= $n - $len; $i++) {
                $subsegments[] = array_slice($arr, $i, $len);
            }
        }
        $subsegments[] = [];
        return $subsegments;
    }

    /**
     * Indique si un professeur est déjà occupé sur d'autres emplois du temps.
     */
    private function hasTeacherConflict(string $teacherId, string $day, string $slotLabel, string $timetableId): bool
    {
        return TimetableEntry::where('day_of_week', $day)
            ->where('slot_number', $slotLabel)
            ->where('timetable_id', '!=', $timetableId)
            ->whereHas('subjectTeacher', function ($q) use ($teacherId) {
                $q->where('teacher_id', $teacherId);
            })->exists();
    }

    /**
     * Retourne la clé numérique d'un créneau à partir de son libellé horaire.
     */
    private function getSlotKeyFromLabel(string $label): ?int
    {
        foreach (self::SLOTS as $key => $data) {
            if ($data['label'] === $label) {
                return $key;
            }
        }
        return null;
    }

    /**
     * Passage 2A : Expansion Contiguë stricte pour combler les trous sans en créer.
     */
    private function runPass2A(Timetable $timetable, Classe $classe, $assignments, array $tempQuotas): array
    {
        $hasChanged = true;

        while ($hasChanged) {
            $hasChanged = false;

            // Rechercher des créneaux libres contigus de la semaine
            $eligibleSlots = [];
            foreach (self::DAYS as $day) {
                // Déterminer les clés de créneaux planifiés pour ce jour
                $scheduledLabels = TimetableEntry::where('timetable_id', $timetable->id)
                    ->where('day_of_week', $day)
                    ->pluck('slot_number')
                    ->toArray();

                $scheduledKeys = [];
                foreach ($scheduledLabels as $lbl) {
                    $key = $this->getSlotKeyFromLabel($lbl);
                    if ($key !== null) {
                        $scheduledKeys[] = $key;
                    }
                }

                foreach (self::SLOTS as $slotKey => $slotData) {
                    if ($this->isSlotAllowedForRegime($classe->regime, $day, $slotKey)) {
                        $alreadyOccupied = TimetableEntry::where('timetable_id', $timetable->id)
                            ->where('day_of_week', $day)
                            ->where('slot_number', $slotData['label'])
                            ->exists();

                        if (!$alreadyOccupied) {
                            // Contiguïté : libre et adjacent à un bloc existant ou journée vide
                            $isContiguous = empty($scheduledKeys) 
                                || in_array($slotKey - 1, $scheduledKeys) 
                                || in_array($slotKey + 1, $scheduledKeys);

                            if ($isContiguous) {
                                $eligibleSlots[] = [
                                    'day' => $day,
                                    'slot_number' => $slotData['label'],
                                    'slot_key' => $slotKey
                                ];
                            }
                        }
                    }
                }
            }

            shuffle($eligibleSlots);

            foreach ($eligibleSlots as $slot) {
                $day = $slot['day'];
                $slotLabel = $slot['slot_number'];
                $shuffledAsms = $assignments->shuffle();

                foreach ($shuffledAsms as $asm) {
                    if ($tempQuotas[$asm->subject_id] < 110) {
                        continue;
                    }

                    $dailyCount = TimetableEntry::where('timetable_id', $timetable->id)
                        ->where('day_of_week', $day)
                        ->whereHas('subjectTeacher', function ($q) use ($asm) {
                            $q->where('subject_id', $asm->subject_id);
                        })->count();

                    if ($dailyCount >= 2) {
                        continue;
                    }

                    if (!$this->isTeacherAvailable($asm->teacher_id, $day, $slotLabel)) {
                        continue;
                    }

                    if ($this->hasTeacherConflict($asm->teacher_id, $day, $slotLabel, $timetable->id)) {
                        continue;
                    }

                    $room = $this->findSuitableFreeRoom($classe, $asm->type, $day, $slotLabel);
                    if (!$room) {
                        continue;
                    }

                    TimetableEntry::create([
                        'id' => (string) Str::uuid(),
                        'timetable_id' => $timetable->id,
                        'subject_teacher_id' => $asm->id,
                        'room_id' => $room->id,
                        'day_of_week' => $day,
                        'slot_number' => $slotLabel,
                        'teacher_status' => 'en_attente',
                    ]);

                    $tempQuotas[$asm->subject_id] -= 110;
                    $hasChanged = true;
                    break 2; // Repartir sur le scan des créneaux
                }
            }
        }

        return $tempQuotas;
    }

    /**
     * Passage 2B : Rattrapage de dernier recours (Mesure Extrême) autorisant des trous si bloqué.
     */
    private function runPass2B(Timetable $timetable, Classe $classe, $assignments, array $tempQuotas): array
    {
        $hasChanged = true;

        while ($hasChanged) {
            $hasChanged = false;

            $eligibleSlots = [];
            foreach (self::DAYS as $day) {
                foreach (self::SLOTS as $slotKey => $slotData) {
                    if ($this->isSlotAllowedForRegime($classe->regime, $day, $slotKey)) {
                        $alreadyOccupied = TimetableEntry::where('timetable_id', $timetable->id)
                            ->where('day_of_week', $day)
                            ->where('slot_number', $slotData['label'])
                            ->exists();

                        if (!$alreadyOccupied) {
                            $eligibleSlots[] = [
                                'day' => $day,
                                'slot_number' => $slotData['label'],
                                'slot_key' => $slotKey
                            ];
                        }
                    }
                }
            }

            shuffle($eligibleSlots);

            foreach ($eligibleSlots as $slot) {
                $day = $slot['day'];
                $slotLabel = $slot['slot_number'];
                $shuffledAsms = $assignments->shuffle();

                foreach ($shuffledAsms as $asm) {
                    if ($tempQuotas[$asm->subject_id] < 110) {
                        continue;
                    }

                    $dailyCount = TimetableEntry::where('timetable_id', $timetable->id)
                        ->where('day_of_week', $day)
                        ->whereHas('subjectTeacher', function ($q) use ($asm) {
                            $q->where('subject_id', $asm->subject_id);
                        })->count();

                    if ($dailyCount >= 2) {
                        continue;
                    }

                    if (!$this->isTeacherAvailable($asm->teacher_id, $day, $slotLabel)) {
                        continue;
                    }

                    if ($this->hasTeacherConflict($asm->teacher_id, $day, $slotLabel, $timetable->id)) {
                        continue;
                    }

                    $room = $this->findSuitableFreeRoom($classe, $asm->type, $day, $slotLabel);
                    if (!$room) {
                        continue;
                    }

                    TimetableEntry::create([
                        'id' => (string) Str::uuid(),
                        'timetable_id' => $timetable->id,
                        'subject_teacher_id' => $asm->id,
                        'room_id' => $room->id,
                        'day_of_week' => $day,
                        'slot_number' => $slotLabel,
                        'teacher_status' => 'en_attente',
                    ]);

                    $tempQuotas[$asm->subject_id] -= 110;
                    $hasChanged = true;
                    break 2; // Repartir sur le scan
                }
            }
        }

        return $tempQuotas;
    }

    /**
     * Vérifie si un créneau horaire est autorisé selon le régime de la classe.
     */
    public function isSlotAllowedForRegime(string $regime, string $day, int $slotKey): bool
    {
        if ($regime === 'J') {
            // Cours du Jour (J) : Généralement créneaux 1 à 4 (08:00 à 17:00)
            return $slotKey >= 1 && $slotKey <= 4;
        } elseif ($regime === 'S') {
            // Cours du Soir (S) : 
            // - Lundi à Vendredi : Créneaux 5 et 6 (17:30 à 21:30)
            // - Samedi : Tous les créneaux (1 à 6)
            if ($day === 'samedi') {
                return $slotKey >= 1 && $slotKey <= 6;
            } else {
                return $slotKey >= 5 && $slotKey <= 6;
            }
        }

        return false;
    }

    /**
     * Vérifie la disponibilité générale d'un enseignant sur un créneau.
     * Si l'enseignant n'a déclaré aucune disponibilité, on suppose qu'il est disponible par défaut.
     * S'il a déclaré des créneaux, il doit y être marqué comme libre.
     */
    public function isTeacherAvailable(string $teacherId, string $day, string $slotLabel): bool
    {
        $hasDeclared = TeacherAvailability::where('teacher_id', $teacherId)->exists();
        if (!$hasDeclared) {
            return true; // Disponible par défaut si non paramétré
        }

        return TeacherAvailability::where('teacher_id', $teacherId)
            ->where('day_of_week', $day)
            ->where('slot_number', $slotLabel)
            ->where('is_available', true)
            ->exists();
    }

    /**
     * Recherche une salle libre et adaptée.
     * - TP : Filtre uniquement les labos (is_labo = true)
     * - CM/TD : Utilise en priorité la salle attitrée de la classe, sinon une salle standard (is_labo = false)
     */
    public function findSuitableFreeRoom(Classe $classe, string $type, string $day, string $slotLabel): ?Room
    {
        if ($type === 'TP') {
            // Rechercher un laboratoire libre
            return Room::where('is_labo', true)
                ->availableAt($day, $slotLabel)
                ->first();
        } else {
            // CM ou TD
            // 1. Vérifier si la salle attitrée de la classe est libre
            if ($classe->room_id) {
                $assignedRoom = Room::find($classe->room_id);
                if ($assignedRoom && !$this->isRoomOccupied($assignedRoom->id, $day, $slotLabel)) {
                    return $assignedRoom;
                }
            }

            // 2. Sinon, prendre n'importe quelle salle de cours standard libre
            return Room::where('is_labo', false)
                ->availableAt($day, $slotLabel)
                ->first();
        }
    }

    /**
     * Indique si une salle est occupée à un jour et créneau donné.
     */
    public function isRoomOccupied(string $roomId, string $day, string $slotLabel): bool
    {
        return TimetableEntry::where('room_id', $roomId)
            ->where('day_of_week', $day)
            ->where('slot_number', $slotLabel)
            ->exists();
    }
}
