<?php

namespace Tests\Feature;

use App\Models\Classe;
use App\Models\TimetableEntry;
use App\Services\TimetableGenerator;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GeneratorTest extends TestCase
{
    use RefreshDatabase;

    public function test_generator_respects_contiguity_and_block_cohesion_rules()
    {
        // 1. Seed database
        $this->seed(DatabaseSeeder::class);

        $generator = new TimetableGenerator();

        // 2. Test for Jour regime class (GLJ2B)
        $classeJour = Classe::where('code_unique', 'GLJ2B')->firstOrFail();
        $timetableJour = $generator->generateForClasse($classeJour->id, (int) date('W'), date('Y'));

        $this->assertNotNull($timetableJour);
        $entriesJour = $timetableJour->entries()->get();
        $this->assertNotEmpty($entriesJour);

        // Group entries by day
        $entriesByDayJour = $entriesJour->groupBy('day_of_week');

        foreach ($entriesByDayJour as $day => $dayEntries) {
            // Get slot keys
            $slotKeys = [];
            foreach ($dayEntries as $entry) {
                foreach (TimetableGenerator::SLOTS as $key => $data) {
                    if ($data['label'] === $entry->slot_number) {
                        $slotKeys[] = $key;
                        break;
                    }
                }
            }

            sort($slotKeys);

            // Assert contiguous (no gaps)
            if (count($slotKeys) > 0) {
                $minKey = min($slotKeys);
                $maxKey = max($slotKeys);
                $this->assertEquals($maxKey - $minKey + 1, count($slotKeys), "Gap detected on day: {$day} for GLJ2B! Keys: " . implode(',', $slotKeys));
            }

            // Assert block cohesion (same-name courses back-to-back, not separated by other subjects)
            // Sequence of subject names must have no duplicate interspersed names.
            $subjectNames = [];
            foreach ($dayEntries as $entry) {
                $subjectNames[] = $entry->subjectTeacher->subject->name;
            }

            // Verify no A, B, A pattern
            $uniqueBlocks = [];
            $lastSubject = null;
            foreach ($subjectNames as $sub) {
                if ($sub !== $lastSubject) {
                    $uniqueBlocks[] = $sub;
                    $lastSubject = $sub;
                }
            }
            $this->assertEquals(count(array_unique($subjectNames)), count($uniqueBlocks), "Separation of same-name subjects detected on day: {$day} for GLJ2B! Sequence: " . implode(' -> ', $subjectNames));
        }

        // 3. Test for Soir regime class (GLS3A)
        $classeSoir = Classe::where('code_unique', 'GLS3A')->firstOrFail();
        $timetableSoir = $generator->generateForClasse($classeSoir->id, (int) date('W'), date('Y'));

        $this->assertNotNull($timetableSoir);
        $entriesSoir = $timetableSoir->entries()->get();
        $this->assertNotEmpty($entriesSoir);

        // Group entries by day
        $entriesByDaySoir = $entriesSoir->groupBy('day_of_week');

        foreach ($entriesByDaySoir as $day => $dayEntries) {
            // Get slot keys
            $slotKeys = [];
            foreach ($dayEntries as $entry) {
                foreach (TimetableGenerator::SLOTS as $key => $data) {
                    if ($data['label'] === $entry->slot_number) {
                        $slotKeys[] = $key;
                        break;
                    }
                }
            }

            sort($slotKeys);

            // Assert contiguous (no gaps)
            if (count($slotKeys) > 0) {
                $minKey = min($slotKeys);
                $maxKey = max($slotKeys);
                $this->assertEquals($maxKey - $minKey + 1, count($slotKeys), "Gap detected on day: {$day} for GLS3A! Keys: " . implode(',', $slotKeys));
            }

            // Assert block cohesion
            $subjectNames = [];
            foreach ($dayEntries as $entry) {
                $subjectNames[] = $entry->subjectTeacher->subject->name;
            }

            $uniqueBlocks = [];
            $lastSubject = null;
            foreach ($subjectNames as $sub) {
                if ($sub !== $lastSubject) {
                    $uniqueBlocks[] = $sub;
                    $lastSubject = $sub;
                }
            }
            $this->assertEquals(count(array_unique($subjectNames)), count($uniqueBlocks), "Separation of same-name subjects detected on day: {$day} for GLS3A! Sequence: " . implode(' -> ', $subjectNames));
        }
    }
}
