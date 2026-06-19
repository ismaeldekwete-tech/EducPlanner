<?php

namespace App\Livewire;

use App\Models\TeacherAvailability;
use App\Models\TimetableEntry;
use App\Models\AuditLog;
use App\Services\TimetableRefusalHandler;
use App\Services\TimetableGenerator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

class TeacherDashboard extends Component
{
    public string $activeTab = 'schedule'; // schedule, availabilities, propositions

    // General Availability Data
    public array $availGrid = []; // [day][slot] => bool

    // Proposition Action Modal
    public bool $showRefusalModal = false;
    public string $selectedEntryId = '';
    public string $refusalReason = '';
    public string $errorMessage = '';

    // Flash Messages
    public string $successMsg = '';
    public string $errorMsg = '';

    public string $selectedWeek = 'current'; // current, next

    public function mount()
    {
        if (!auth()->user() || !auth()->user()->hasRole('Professeur')) {
            abort(403, 'Accès réservé aux professeurs.');
        }

        $this->loadAvailabilities();
    }

    public function loadAvailabilities()
    {
        $teacher = auth()->user();
        if (!$teacher) return;

        $slots = [
            '08:00 - 09:50', '10:10 - 12:00', '13:00 - 14:50',
            '15:10 - 17:00', '17:30 - 19:30', '20:00 - 21:30'
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
    }

    public function toggleAvail(string $day, string $slot)
    {
        $this->availGrid[$day][$slot] = !$this->availGrid[$day][$slot];
    }

    public function saveAvailabilities()
    {
        $teacher = auth()->user();
        if (!$teacher) return;

        DB::transaction(function () use ($teacher) {
            TeacherAvailability::where('teacher_id', $teacher->id)->delete();

            foreach ($this->availGrid as $day => $slots) {
                foreach ($slots as $slot => $isAvailable) {
                    if ($isAvailable) {
                        TeacherAvailability::create([
                            'id' => (string) Str::uuid(),
                            'teacher_id' => $teacher->id,
                            'day_of_week' => $day,
                            'slot_number' => $slot,
                            'is_available' => true,
                        ]);
                    }
                }
            }
        });

        $this->successMsg = __('Vos disponibilités hebdomadaires ont été mises à jour avec succès !');
        AuditLog::log('UPDATE_MY_AVAILABILITIES', ['teacher' => $teacher->name]);
    }

    public function confirmEntry(string $entryId)
    {
        $entry = TimetableEntry::with(['subjectTeacher.subject', 'timetable.classe'])->findOrFail($entryId);
        
        if ($entry->subjectTeacher->teacher_id !== auth()->id()) {
            $this->errorMsg = __('Vous n\'êtes pas autorisé à valider ce cours.');
            return;
        }

        $entry->update(['teacher_status' => 'confirme']);
        
        AuditLog::log('TEACHER_CONFIRM_SLOT', [
            'teacher' => auth()->user()->name,
            'subject' => $entry->subjectTeacher->subject->name,
            'classe' => $entry->timetable->classe->code_unique,
            'day' => $entry->day_of_week,
            'slot' => $entry->slot_number
        ]);

        $this->successMsg = __('Vous avez confirmé votre présence pour ce cours !');
    }

    public function openRefusal(string $entryId)
    {
        $this->selectedEntryId = $entryId;
        $this->refusalReason = '';
        $this->errorMessage = '';
        $this->showRefusalModal = true;
    }

    public function submitRefusal(TimetableRefusalHandler $refusalHandler)
    {
        $this->validate([
            'refusalReason' => 'required|string|min:10|max:500'
        ]);

        $entry = TimetableEntry::with(['subjectTeacher.subject', 'timetable.classe'])->findOrFail($this->selectedEntryId);

        if ($entry->subjectTeacher->teacher_id !== auth()->id()) {
            $this->errorMsg = __('Vous n\'êtes pas autorisé à refuser ce cours.');
            $this->showRefusalModal = false;
            return;
        }

        try {
            // Trigger intelligent refusal handler (with automatic replacement)
            $replacedEntry = $refusalHandler->handleRefusal($entry->id, $this->refusalReason);

            $this->showRefusalModal = false;
            
            if ($replacedEntry) {
                $this->successMsg = __('Votre refus a été enregistré. Un cours alternatif a été automatiquement planifié à votre place.');
            } else {
                $this->successMsg = __('Votre refus a été enregistré. Le créneau a été libéré pour l\'administration.');
            }
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function render()
    {
        $teacher = auth()->user();

        // 1. All weekly timetable entries scheduled for this teacher
        if ($this->selectedWeek === 'next') {
            $nextWeekTime = strtotime('+1 week');
            $week = (int) date('W', $nextWeekTime);
            $academicYear = date('Y', $nextWeekTime);

            $myEntries = TimetableEntry::with(['subjectTeacher.subject', 'timetable.classe', 'room'])
                ->whereHas('subjectTeacher', function ($q) use ($teacher) {
                    $q->where('teacher_id', $teacher->id);
                })
                ->whereHas('timetable', function ($q) use ($week, $academicYear) {
                    $q->where('week_number', $week)
                      ->where('academic_year', $academicYear)
                      ->where('status', 'publie'); // ONLY show if officially published
                })
                ->get();
        } else {
            $week = (int) date('W');
            $academicYear = date('Y');

            $myEntries = TimetableEntry::with(['subjectTeacher.subject', 'timetable.classe', 'room'])
                ->whereHas('subjectTeacher', function ($q) use ($teacher) {
                    $q->where('teacher_id', $teacher->id);
                })
                ->whereHas('timetable', function ($q) use ($week, $academicYear) {
                    $q->where('week_number', $week)
                      ->where('academic_year', $academicYear);
                })
                ->get();
        }

        // 2. Pending propositions specifically waiting for approval (only make sense for current or drafts)
        $pendingPropositions = $myEntries->where('teacher_status', 'en_attente');

        // 3. Official confirmed grid entries
        $confirmedEntries = $myEntries->where('teacher_status', '!=', 'refuse');

        $days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
        $slots = [
            ['label' => '08:00 - 09:50'],
            ['label' => '10:10 - 12:00'],
            ['label' => '13:00 - 14:50'],
            ['label' => '15:10 - 17:00'],
            ['label' => '17:30 - 19:30'],
            ['label' => '20:00 - 21:30'],
        ];

        return view('livewire.teacher-dashboard', [
            'myEntries' => $myEntries,
            'pendingPropositions' => $pendingPropositions,
            'confirmedEntries' => $confirmedEntries,
            'days' => $days,
            'slots' => $slots,
            'teacher' => $teacher
        ])->layout('layouts.app');
    }
}
