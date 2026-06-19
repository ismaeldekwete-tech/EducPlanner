<?php

namespace App\Livewire;

use App\Models\AuditLog;
use App\Models\Classe;
use App\Models\Department;
use App\Models\Room;
use App\Models\Subject;
use App\Models\SubjectTeacher;
use App\Models\TeacherAvailability;
use App\Models\Timetable;
use App\Models\TimetableEntry;
use App\Models\User;
use App\Services\TimetableGenerator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class TimetableManager extends Component
{
    use WithPagination;

    // Tabs navigation
    public string $activeTab = 'grid'; // grid, teachers, rooms, classes, subjects, assignments, audit_logs

    // Filters & Selections
    public string $selectedClasseId = '';

    public string $selectedWeekKey = '';

    public array $availableWeeks = [];

    public $entries = [];

    public ?Timetable $currentTimetable = null;

    // Grid interaction modal
    public bool $showGridModal = false;

    public string $editingDay = '';

    public string $editingSlot = '';

    public string $selectedAssignmentId = '';

    public string $selectedRoomId = '';

    public bool $forceQuota = false;

    public string $gridErrorMessage = '';

    // Teacher Management
    public string $teacherId = '';

    public string $teacherName = '';

    public string $teacherEmail = '';

    public string $teacherPhone = '';

    public string $teacherPassword = '';

    public bool $showTeacherModal = false;

    // Teacher Availabilities Checklist
    public bool $showAvailabilitiesModal = false;

    public string $availTeacherId = '';

    public string $availTeacherName = '';

    public array $availGrid = []; // [day][slot_label] => bool

    // Room Management
    public string $roomId = '';

    public string $roomName = '';

    public string $roomCapacity = '';

    public bool $roomIsLabo = false;

    public bool $showRoomModal = false;

    // Class Management
    public string $classId = '';

    public string $classFiliere = '';

    public string $classRegime = 'J'; // J, S

    public int $classNiveau = 1;

    public string $classGroupe = 'A';

    public string $classCodeUnique = '';

    public string $classRoomId = '';

    public bool $showClassModal = false;

    // Subject Management
    public string $subjectId = '';

    public string $subjectName = '';

    public string $subjectCode = '';

    public int $subjectSemester = 1;

    public int $quotaCm = 0;

    public int $quotaTd = 0;

    public int $quotaTp = 0;

    public bool $showSubjectModal = false;

    // Assignment (SubjectTeacher) Management
    public string $asmId = '';

    public string $asmSubjectId = '';

    public string $asmTeacherId = '';

    public string $asmClasseId = '';

    public string $asmType = 'CM'; // CM, TD, TP

    public bool $showAsmModal = false;

    // Flash Messages
    public string $successMsg = '';

    public string $errorMsg = '';

    protected $queryString = ['activeTab', 'selectedWeekKey'];

    public function mount()
    {
        if (! auth()->user() || ! auth()->user()->hasAnyRole(['SuperAdmin', 'ChefDepartement'])) {
            abort(403, 'Accès réservé aux administrateurs.');
        }

        $this->selectedWeekKey = Carbon::now()->addWeek()->format('o-W');

        $firstClass = Classe::first();
        if ($firstClass) {
            $this->selectedClasseId = $firstClass->id;
            $this->loadWeekOptions();
            $this->loadTimetable();
        }
    }

    public function updatedSelectedClasseId()
    {
        $this->loadWeekOptions();
        $this->loadTimetable();
        $this->resetErrorBag();
    }

    public function updatedSelectedWeekKey()
    {
        $this->loadTimetable();
    }

    protected function loadWeekOptions()
    {
        if (! $this->selectedClasseId) {
            $this->availableWeeks = [];

            return;
        }

        $nextWeek = Carbon::now()->addWeek();
        $nextKey = $nextWeek->format('o-W');

        $timetables = Timetable::where('classe_id', $this->selectedClasseId)
            ->orderByDesc('academic_year')
            ->orderByDesc('week_number')
            ->get();

        $options = [];
        foreach ($timetables as $timetable) {
            $key = sprintf('%s-%02d', $timetable->academic_year, $timetable->week_number);
            $statusLabel = $timetable->status === 'publie' ? __('Publié') : __('Brouillon');
            $options[$key] = __('Semaine :week - :year (:status)', [
                'week' => $timetable->week_number,
                'year' => $timetable->academic_year,
                'status' => $statusLabel,
            ]);
        }

        if (! isset($options[$nextKey])) {
            $options = [$nextKey => __('Semaine suivante (:week - :year)', [
                'week' => $nextWeek->format('W'),
                'year' => $nextWeek->format('Y'),
            ])] + $options;
        }

        $this->availableWeeks = $options;

        if (! $this->selectedWeekKey || ! isset($this->availableWeeks[$this->selectedWeekKey])) {
            $this->selectedWeekKey = $nextKey;
        }
    }

    protected function parseSelectedWeek(): array
    {
        if (! $this->selectedWeekKey || ! str_contains($this->selectedWeekKey, '-')) {
            $next = Carbon::now()->addWeek();

            return [(int) $next->format('W'), $next->format('Y')];
        }

        [$year, $week] = explode('-', $this->selectedWeekKey);

        return [(int) $week, $year];
    }

    public function loadTimetable()
    {
        if (! $this->selectedClasseId) {
            $this->entries = [];
            $this->currentTimetable = null;

            return;
        }

        [$weekNumber, $academicYear] = $this->parseSelectedWeek();

        $this->currentTimetable = Timetable::where('classe_id', $this->selectedClasseId)
            ->where('week_number', $weekNumber)
            ->where('academic_year', $academicYear)
            ->first();

        $this->entries = $this->currentTimetable ? $this->currentTimetable->entries()->with(['subjectTeacher.subject', 'subjectTeacher.teacher', 'room'])->get()->toArray() : [];
    }

    // --- TIMETABLE GRID GENERATION ---
    public function generate(TimetableGenerator $generator)
    {
        if (! $this->selectedClasseId) {
            return;
        }

        [$weekNumber, $academicYear] = $this->parseSelectedWeek();

        $timetable = Timetable::where('classe_id', $this->selectedClasseId)
            ->where('week_number', $weekNumber)
            ->where('academic_year', $academicYear)
            ->first();

        if ($timetable && $timetable->status === 'publie') {
            $this->errorMsg = __('Cet emploi du temps est déjà publié et verrouillé. Dépubliez-le d\'abord.');
            return;
        }

        try {
            $generator->generateForClasse($this->selectedClasseId, $weekNumber, $academicYear);
            $this->loadTimetable();
            $this->successMsg = __('Emploi du temps généré automatiquement avec succès !');
        } catch (\Exception $e) {
            $this->errorMsg = $e->getMessage();
        }
    }

    public function sendRequests()
    {
        if (! $this->currentTimetable) {
            return;
        }

        if ($this->currentTimetable->status === 'publie') {
            $this->errorMsg = __('Cet emploi du temps est publié et verrouillé.');
            return;
        }

        DB::transaction(function () {
            $this->currentTimetable->update(['status' => 'brouillon']); // Status transition

            // Mark all entries as en_attente and notify
            foreach ($this->currentTimetable->entries as $entry) {
                $entry->update(['teacher_status' => 'en_attente']);

                // Envoi de notification (simulation mail via maildev)
                try {
                    $teacher = $entry->subjectTeacher->teacher;
                    $subject = $entry->subjectTeacher->subject;
                    $classe = $this->currentTimetable->classe;

                    Mail::raw(
                        "Bonjour {$teacher->name},\n\nUn nouveau cours vous est proposé pour validation dans l'emploi du temps de la classe {$classe->code_unique} :\n".
                        "- Cours : {$subject->name} ({$entry->subjectTeacher->type})\n".
                        "- Jour : {$entry->day_of_week}\n".
                        "- Créneau : {$entry->slot_number}\n\n".
                        "Merci de vous connecter dans l'application pour confirmer ou refuser ce créneau.\n\n".
                        "Cordialement,\nL'administration.",
                        function ($message) use ($teacher) {
                            $message->to($teacher->email)
                                ->subject('EducPlanner : Demande de validation de cours');
                        }
                    );
                } catch (\Exception $e) {
                }
            }
        });

        $this->loadTimetable();
        $this->successMsg = __('Les demandes de disponibilité ont été envoyées aux professeurs dans l\'application !');
        AuditLog::log('SEND_TEACHER_REQUESTS', ['classe' => $this->currentTimetable->classe->code_unique]);
    }

    public function publish()
    {
        if (! $this->currentTimetable) {
            return;
        }

        if ($this->currentTimetable->status === 'publie') {
            $this->errorMsg = __('Cet emploi du temps est déjà publié.');

            return;
        }

        try {
            DB::transaction(function () {
                // Décrémenter les quotas uniquement lors de la publication officielle
                foreach ($this->currentTimetable->entries as $entry) {
                    $subject = $entry->subjectTeacher->subject;
                    // Décrémente de 110 minutes
                    $subject->decrement('quota_total_remaining_minutes', 110);
                }

                $this->currentTimetable->update(['status' => 'publie']);
            });

            $this->loadTimetable();
            $this->successMsg = __('L\'emploi du temps a été officiellement publié ! Les quotas ont été décrémentés.');
            AuditLog::log('PUBLISH_TIMETABLE', ['classe' => $this->currentTimetable->classe->code_unique]);
        } catch (\Exception $e) {
            $this->errorMsg = $e->getMessage();
        }
    }

    public function depublish()
    {
        if (! $this->currentTimetable) {
            return;
        }

        if ($this->currentTimetable->status !== 'publie') {
            $this->errorMsg = __('Cet emploi du temps n\'est pas publié.');
            return;
        }

        try {
            DB::transaction(function () {
                // Ré-incrémenter les quotas des matières de chaque entrée de 110 minutes
                foreach ($this->currentTimetable->entries as $entry) {
                    if ($entry->subjectTeacher && $entry->subjectTeacher->subject) {
                        $subject = $entry->subjectTeacher->subject;
                        $subject->increment('quota_total_remaining_minutes', 110);
                    }
                }

                $this->currentTimetable->update(['status' => 'brouillon']);
            });

            $this->loadTimetable();
            $this->successMsg = __('L\'emploi du temps a été dépublié. Les quotas ont été recrédités.');
            AuditLog::log('DEPUBLISH_TIMETABLE', ['classe' => $this->currentTimetable->classe->code_unique]);
        } catch (\Exception $e) {
            $this->errorMsg = $e->getMessage();
        }
    }

    // --- MANUAL EDIT DIALOG ---
    public function openCellEdit(string $day, string $slotLabel)
    {
        if ($this->currentTimetable && $this->currentTimetable->status === 'publie') {
            $this->errorMsg = __('Cet emploi du temps est publié et verrouillé. Dépubliez-le d\'abord.');
            return;
        }

        $this->editingDay = $day;
        $this->editingSlot = $slotLabel;
        $this->selectedAssignmentId = '';
        $this->selectedRoomId = '';
        $this->forceQuota = false;
        $this->gridErrorMessage = '';

        // Si une entrée existe déjà à ce créneau, charger ses valeurs
        $existing = null;
        if ($this->currentTimetable) {
            foreach ($this->entries as $entry) {
                if ($entry['day_of_week'] === strtolower($day) && $entry['slot_number'] === $slotLabel) {
                    $existing = $entry;
                    break;
                }
            }
        }

        if ($existing) {
            $this->selectedAssignmentId = $existing['subject_teacher_id'];
            $this->selectedRoomId = $existing['room_id'];
        }

        $this->showGridModal = true;
    }

    public function saveCell()
    {
        $this->gridErrorMessage = '';

        if (! $this->selectedClasseId) {
            return;
        }

        if ($this->currentTimetable && $this->currentTimetable->status === 'publie') {
            $this->gridErrorMessage = __('Cet emploi du temps est publié et verrouillé.');
            return;
        }

        // Si l'utilisateur n'a rien sélectionné, on libère le créneau
        if (! $this->selectedAssignmentId) {
            if ($this->currentTimetable) {
                TimetableEntry::where('timetable_id', $this->currentTimetable->id)
                    ->where('day_of_week', strtolower($this->editingDay))
                    ->where('slot_number', $this->editingSlot)
                    ->delete();
            }
            $this->showGridModal = false;
            $this->loadTimetable();
            $this->successMsg = __('Créneau libéré avec succès.');

            return;
        }

        // Charger l'affectation et la salle
        $asm = SubjectTeacher::with(['subject', 'teacher'])->findOrFail($this->selectedAssignmentId);
        $room = Room::findOrFail($this->selectedRoomId);

        // Validation physique des conflits
        // 1. Conflit de Salle : est-elle occupée par une autre classe ce jour-là à ce créneau ?
        $roomConflict = TimetableEntry::where('room_id', $room->id)
            ->where('day_of_week', strtolower($this->editingDay))
            ->where('slot_number', $this->editingSlot)
            ->where('timetable_id', '!=', $this->currentTimetable->id ?? '')
            ->exists();

        if ($roomConflict) {
            $this->gridErrorMessage = __('Conflit physique : La salle sélectionnée est déjà occupée à ce créneau par une autre classe.');

            return;
        }

        // 2. Conflit d'Enseignant : est-il occupé ailleurs ce jour-là à ce créneau ?
        $teacherConflict = TimetableEntry::where('day_of_week', strtolower($this->editingDay))
            ->where('slot_number', $this->editingSlot)
            ->where('timetable_id', '!=', $this->currentTimetable->id ?? '')
            ->whereHas('subjectTeacher', function ($q) use ($asm) {
                $q->where('teacher_id', $asm->teacher_id);
            })->exists();

        if ($teacherConflict) {
            $this->gridErrorMessage = __('Conflit de Professeur : Cet enseignant donne déjà un cours à ce créneau dans une autre classe.');

            return;
        }

        // 3. Vérification de la disponibilité déclarée du professeur
        $generator = new TimetableGenerator;
        if (! $generator->isTeacherAvailable($asm->teacher_id, strtolower($this->editingDay), $this->editingSlot)) {
            $this->gridErrorMessage = __('Avertissement : Le professeur a déclaré être indisponible à ce créneau.');

            return;
        }

        // 4. Vérification du quota de la matière
        if ($asm->subject->quota_total_remaining_minutes < 110 && ! $this->forceQuota) {
            $this->gridErrorMessage = __('Le quota d\'heures de cette matière est épuisé. Cochez "Forcer le quota" pour passer outre.');

            return;
        }

        // Tout est OK, enregistrer
        [$weekNumber, $academicYear] = $this->parseSelectedWeek();

        DB::transaction(function () use ($asm, $room, $weekNumber, $academicYear) {
            $timetable = Timetable::firstOrCreate([
                'classe_id' => $this->selectedClasseId,
                'week_number' => $weekNumber,
                'academic_year' => $academicYear,
            ], [
                'status' => 'brouillon',
                'created_by' => auth()->id() ?? User::first()->id,
            ]);

            // Supprimer l'ancienne entrée à ce créneau s'il y en avait une
            TimetableEntry::where('timetable_id', $timetable->id)
                ->where('day_of_week', strtolower($this->editingDay))
                ->where('slot_number', $this->editingSlot)
                ->delete();

            // Insérer la nouvelle entrée
            TimetableEntry::create([
                'id' => (string) Str::uuid(),
                'timetable_id' => $timetable->id,
                'subject_teacher_id' => $asm->id,
                'room_id' => $room->id,
                'day_of_week' => strtolower($this->editingDay),
                'slot_number' => $this->editingSlot,
                'teacher_status' => 'en_attente',
            ]);

            if ($this->forceQuota && $asm->subject->quota_total_remaining_minutes < 110) {
                AuditLog::log('FORCE_QUOTA', [
                    'classe' => $timetable->classe->code_unique,
                    'subject' => $asm->subject->name,
                    'day' => $this->editingDay,
                    'slot' => $this->editingSlot,
                ]);
            }
        });

        $this->showGridModal = false;
        $this->loadTimetable();
        $this->successMsg = __('Créneau planifié manuellement.');
    }

    // --- CRUD: PROFESSORS ---
    public function openTeacherCreate()
    {
        $this->teacherId = '';
        $this->teacherName = '';
        $this->teacherEmail = '';
        $this->teacherPhone = '';
        $this->teacherPassword = '';
        $this->showTeacherModal = true;
    }

    public function editTeacher(string $id)
    {
        $teacher = User::findOrFail($id);
        $this->teacherId = $teacher->id;
        $this->teacherName = $teacher->name;
        $this->teacherEmail = $teacher->email;
        $this->teacherPhone = $teacher->phone ?? '';
        $this->teacherPassword = '';
        $this->showTeacherModal = true;
    }

    public function saveTeacher()
    {
        $rules = [
            'teacherName' => 'required|string',
            'teacherEmail' => 'required|email|unique:users,email,'.($this->teacherId ?: 'NULL').',id',
            'teacherPhone' => 'nullable|string',
        ];

        if (! $this->teacherId) {
            $rules['teacherPassword'] = 'required|string|min:6';
        }

        $this->validate($rules);

        $dept = Department::first();

        if ($this->teacherId) {
            $teacher = User::findOrFail($this->teacherId);
            $teacher->update([
                'name' => $this->teacherName,
                'email' => $this->teacherEmail,
                'phone' => $this->teacherPhone,
            ]);
            if ($this->teacherPassword) {
                $teacher->update(['password' => Hash::make($this->teacherPassword)]);
            }
            $this->successMsg = __('Enseignant mis à jour.');
        } else {
            $teacher = User::create([
                'name' => $this->teacherName,
                'email' => $this->teacherEmail,
                'phone' => $this->teacherPhone,
                'password' => Hash::make($this->teacherPassword),
                'department_id' => $dept->id ?? null,
            ]);
            $teacher->assignRole('Professeur');
            $this->successMsg = __('Enseignant créé.');
        }

        $this->showTeacherModal = false;
    }

    public function deleteTeacher(string $id)
    {
        User::findOrFail($id)->delete();
        $this->successMsg = __('Enseignant supprimé.');
    }

    public function manageAvailabilities(string $id)
    {
        $teacher = User::findOrFail($id);
        $this->availTeacherId = $teacher->id;
        $this->availTeacherName = $teacher->name;

        // Préparer la grille
        $slots = [
            '08:00 - 09:50', '10:10 - 12:00', '13:00 - 14:50',
            '15:10 - 17:00', '17:30 - 19:30', '20:00 - 21:30',
        ];
        $days = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'];

        $this->availGrid = [];
        foreach ($days as $day) {
            foreach ($slots as $slot) {
                $this->availGrid[$day][$slot] = false;
            }
        }

        $declared = TeacherAvailability::where('teacher_id', $teacher->id)->get();
        foreach ($declared as $av) {
            $this->availGrid[$av->day_of_week][$av->slot_number] = (bool) $av->is_available;
        }

        $this->showAvailabilitiesModal = true;
    }

    public function toggleAvail(string $day, string $slot)
    {
        $this->availGrid[$day][$slot] = ! $this->availGrid[$day][$slot];
    }

    public function saveAvailabilities()
    {
        DB::transaction(function () {
            TeacherAvailability::where('teacher_id', $this->availTeacherId)->delete();

            foreach ($this->availGrid as $day => $slots) {
                foreach ($slots as $slot => $isAvailable) {
                    if ($isAvailable) {
                        TeacherAvailability::create([
                            'id' => (string) Str::uuid(),
                            'teacher_id' => $this->availTeacherId,
                            'day_of_week' => $day,
                            'slot_number' => $slot,
                            'is_available' => true,
                        ]);
                    }
                }
            }
        });

        $this->showAvailabilitiesModal = false;
        $this->successMsg = __('Disponibilités générales mises à jour.');
    }

    // --- CRUD: ROOMS ---
    public function openRoomCreate()
    {
        $this->roomId = '';
        $this->roomName = '';
        $this->roomCapacity = '';
        $this->roomIsLabo = false;
        $this->showRoomModal = true;
    }

    public function editRoom(string $id)
    {
        $room = Room::findOrFail($id);
        $this->roomId = $room->id;
        $this->roomName = $room->name;
        $this->roomCapacity = $room->capacity ?? '';
        $this->roomIsLabo = (bool) $room->is_labo;
        $this->showRoomModal = true;
    }

    public function saveRoom()
    {
        $this->validate([
            'roomName' => 'required|string',
            'roomCapacity' => 'nullable|integer',
        ]);

        if ($this->roomId) {
            Room::findOrFail($this->roomId)->update([
                'name' => $this->roomName,
                'capacity' => $this->roomCapacity ?: null,
                'is_labo' => $this->roomIsLabo,
            ]);
            $this->successMsg = __('Salle mise à jour.');
        } else {
            Room::create([
                'name' => $this->roomName,
                'capacity' => $this->roomCapacity ?: null,
                'is_labo' => $this->roomIsLabo,
            ]);
            $this->successMsg = __('Salle créée.');
        }

        $this->showRoomModal = false;
    }

    public function deleteRoom(string $id)
    {
        Room::findOrFail($id)->delete();
        $this->successMsg = __('Salle supprimée.');
    }

    // --- CRUD: CLASSES ---
    public function openClassCreate()
    {
        $this->classId = '';
        $this->classFiliere = '';
        $this->classRegime = 'J';
        $this->classNiveau = 1;
        $this->classGroupe = 'A';
        $this->classCodeUnique = '';
        $this->classRoomId = '';
        $this->showClassModal = true;
    }

    public function editClass(string $id)
    {
        $class = Classe::findOrFail($id);
        $this->classId = $class->id;
        $this->classFiliere = $class->filiere;
        $this->classRegime = $class->regime;
        $this->classNiveau = $class->niveau;
        $this->classGroupe = $class->groupe;
        $this->classCodeUnique = $class->code_unique;
        $this->classRoomId = $class->room_id ?? '';
        $this->showClassModal = true;
    }

    public function saveClass()
    {
        $this->validate([
            'classFiliere' => 'required|string',
            'classCodeUnique' => 'required|string|unique:classes,code_unique,'.($this->classId ?: 'NULL').',id',
        ]);

        $dept = Department::first();

        $data = [
            'filiere' => $this->classFiliere,
            'regime' => $this->classRegime,
            'niveau' => $this->classNiveau,
            'groupe' => $this->classGroupe,
            'code_unique' => $this->classCodeUnique,
            'department_id' => $dept->id ?? null,
            'room_id' => $this->classRoomId ?: null,
        ];

        if ($this->classId) {
            Classe::findOrFail($this->classId)->update($data);
            $this->successMsg = __('Classe mise à jour.');
        } else {
            Classe::create($data);
            $this->successMsg = __('Classe créée.');
        }

        $this->showClassModal = false;
    }

    public function deleteClass(string $id)
    {
        Classe::findOrFail($id)->delete();
        $this->successMsg = __('Classe supprimée.');
    }

    // --- CRUD: SUBJECTS ---
    public function openSubjectCreate()
    {
        $this->subjectId = '';
        $this->subjectName = '';
        $this->subjectCode = '';
        $this->subjectSemester = 1;
        $this->quotaCm = 0;
        $this->quotaTd = 0;
        $this->quotaTp = 0;
        $this->showSubjectModal = true;
    }

    public function editSubject(string $id)
    {
        $sub = Subject::findOrFail($id);
        $this->subjectId = $sub->id;
        $this->subjectName = $sub->name;
        $this->subjectCode = $sub->code;
        $this->subjectSemester = $sub->semester;
        $this->quotaCm = (int) $sub->quota_cm_minutes;
        $this->quotaTd = (int) $sub->quota_td_minutes;
        $this->quotaTp = (int) $sub->quota_tp_minutes;
        $this->showSubjectModal = true;
    }

    public function saveSubject()
    {
        $this->validate([
            'subjectName' => 'required|string',
            'subjectCode' => 'required|string|unique:subjects,code,'.($this->subjectId ?: 'NULL').',id',
        ]);

        $dept = Department::first();
        $totalQuota = $this->quotaCm + $this->quotaTd + $this->quotaTp;

        $data = [
            'name' => $this->subjectName,
            'code' => $this->subjectCode,
            'semester' => $this->subjectSemester,
            'quota_cm_minutes' => $this->quotaCm,
            'quota_td_minutes' => $this->quotaTd,
            'quota_tp_minutes' => $this->quotaTp,
            'quota_total_remaining_minutes' => $totalQuota,
            'department_id' => $dept->id ?? null,
        ];

        if ($this->subjectId) {
            Subject::findOrFail($this->subjectId)->update($data);
            $this->successMsg = __('Matière mise à jour.');
        } else {
            Subject::create($data);
            $this->successMsg = __('Matière créée.');
        }

        $this->showSubjectModal = false;
    }

    public function deleteSubject(string $id)
    {
        Subject::findOrFail($id)->delete();
        $this->successMsg = __('Matière supprimée.');
    }

    // --- CRUD: ASSIGNMENTS ---
    public function openAsmCreate()
    {
        $this->asmId = '';
        $this->asmSubjectId = '';
        $this->asmTeacherId = '';
        $this->asmClasseId = '';
        $this->asmType = 'CM';
        $this->showAsmModal = true;
    }

    public function editAsm(string $id)
    {
        $asm = SubjectTeacher::findOrFail($id);
        $this->asmId = $asm->id;
        $this->asmSubjectId = $asm->subject_id;
        $this->asmTeacherId = $asm->teacher_id;
        $this->asmClasseId = $asm->classe_id;
        $this->asmType = $asm->type;
        $this->showAsmModal = true;
    }

    public function saveAsm()
    {
        $this->validate([
            'asmSubjectId' => 'required',
            'asmTeacherId' => 'required',
            'asmClasseId' => 'required',
        ]);

        $data = [
            'subject_id' => $this->asmSubjectId,
            'teacher_id' => $this->asmTeacherId,
            'classe_id' => $this->asmClasseId,
            'type' => $this->asmType,
        ];

        if ($this->asmId) {
            SubjectTeacher::findOrFail($this->asmId)->update($data);
            $this->successMsg = __('Affectation mise à jour.');
        } else {
            SubjectTeacher::create($data);
            $this->successMsg = __('Affectation créée.');
        }

        $this->showAsmModal = false;
    }

    public function deleteAsm(string $id)
    {
        SubjectTeacher::findOrFail($id)->delete();
        $this->successMsg = __('Affectation supprimée.');
    }

    public function render()
    {
        // Lists for CRUD rendering
        $classes = Classe::all();
        $rooms = Room::all();
        $subjects = Subject::all();
        $teachers = User::role('Professeur')->get();
        $assignments = SubjectTeacher::with(['subject', 'teacher', 'classe'])->get();

        // Paginated Audit Logs
        $auditLogs = AuditLog::with('user')->latest()->paginate(15);

        // Filter assignments for manual setup dropdown (only of the selected class)
        $classAssignments = SubjectTeacher::with(['subject', 'teacher'])
            ->where('classe_id', $this->selectedClasseId)
            ->get();

        $days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
        $slots = [
            ['label' => '08:00 - 09:50'],
            ['label' => '10:10 - 12:00'],
            ['label' => '13:00 - 14:50'],
            ['label' => '15:10 - 17:00'],
            ['label' => '17:30 - 19:30'],
            ['label' => '20:00 - 21:30'],
        ];

        return view('livewire.timetable-manager', [
            'classes' => $classes,
            'rooms' => $rooms,
            'subjects' => $subjects,
            'teachers' => $teachers,
            'assignments' => $assignments,
            'classAssignments' => $classAssignments,
            'auditLogs' => $auditLogs,
            'days' => $days,
            'slots' => $slots,
        ])->layout('layouts.app');
    }
}
