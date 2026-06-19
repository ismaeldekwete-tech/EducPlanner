<?php

namespace Database\Seeders;

use App\Models\Classe;
use App\Models\Department;
use App\Models\Room;
use App\Models\Subject;
use App\Models\SubjectTeacher;
use App\Models\TeacherAvailability;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Services\TimetableGenerator;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Création des rôles Spatie
        $adminRole = Role::firstOrCreate(['name' => 'SuperAdmin']);
        $chefDepRole = Role::firstOrCreate(['name' => 'ChefDepartement']);
        $profRole = Role::firstOrCreate(['name' => 'Professeur']);
        $etudiantRole = Role::firstOrCreate(['name' => 'Etudiant']);

        // 2. Création du Super Admin par défaut
        $superAdmin = User::create([
            'name' => 'Directeur 3IAC',
            'email' => 'admin3iac@iuc.cm',
            'password' => Hash::make('Admin3IAC'),
        ]);
        $superAdmin->assignRole($adminRole);

        // 3. Création du Département 3IAC
        $dept = Department::create([
            'name' => 'Institut d\'Ingénierie Informatique d\'Afrique Centrale',
            'code' => '3IAC',
        ]);

        // 4. Création du Chef de Département
        $chef = User::create([
            'name' => 'Chef de Département GL',
            'email' => 'chefgl@iuc.cm',
            'password' => Hash::make('ChefGLPassword'),
            'department_id' => $dept->id,
            'phone' => '+237 600 000 001',
        ]);
        $chef->assignRole($chefDepRole);

        // 5. Création des Salles de cours (Standards et Laboratoires)
        $roomAK306 = Room::create(['name' => 'AK306 AKWA', 'capacity' => null, 'is_labo' => false]);
        $roomAK001 = Room::create(['name' => 'AK001 AKWA', 'capacity' => null, 'is_labo' => false]);
        $roomAK105 = Room::create(['name' => 'AK105 AKWA', 'capacity' => null, 'is_labo' => false]);
        $roomAK106 = Room::create(['name' => 'AK106 LAB', 'capacity' => 30, 'is_labo' => true]);
        $roomAK201 = Room::create(['name' => 'AK201 LAB', 'capacity' => 25, 'is_labo' => true]);

        // 6. Création des Classes (Jour et Soir)
        $classeJour = Classe::create([
            'filiere' => 'GL',
            'regime' => 'J',
            'niveau' => 2,
            'groupe' => 'B',
            'code_unique' => 'GLJ2B',
            'department_id' => $dept->id,
            'room_id' => $roomAK306->id,
        ]);

        $classeSoir = Classe::create([
            'filiere' => 'GL',
            'regime' => 'S',
            'niveau' => 3,
            'groupe' => 'A',
            'code_unique' => 'GLS3A',
            'department_id' => $dept->id,
            'room_id' => $roomAK105->id,
        ]);

        // 7. Création des Professeurs
        $teachersData = [
            ['name' => 'Dr Nguena Justin', 'email' => 'nguena@iuc.cm', 'phone' => '+237 699 999 001'],
            ['name' => 'M. Talla Aubin', 'email' => 'talla@iuc.cm', 'phone' => '+237 699 999 002'],
            ['name' => 'Mme Tchinda Kengne Ricado', 'email' => 'tchinda@iuc.cm', 'phone' => '+237 699 999 003'],
            ['name' => 'M. Kinfack Jeutsa', 'email' => 'kinfack@iuc.cm', 'phone' => '+237 699 999 004'],
        ];

        $teachers = [];
        foreach ($teachersData as $tData) {
            $teacher = User::create([
                'name' => $tData['name'],
                'email' => $tData['email'],
                'password' => Hash::make('TeacherPassword'),
                'department_id' => $dept->id,
                'phone' => $tData['phone'],
            ]);
            $teacher->assignRole($profRole);
            $teachers[$tData['email']] = $teacher;
        }

        // 8. Création des Matières avec Quotas (cm, td, tp) stockés en minutes (30 heures = 1800 minutes)
        // 1 période = 110 minutes
        $subjectsData = [
            [
                'name' => 'Algorithmique et Programmation',
                'code' => 'GL201',
                'semester' => 1,
                'quota_cm' => 1320, // 12 créneaux
                'quota_td' => 660,  // 6 créneaux
                'quota_tp' => 1320, // 12 créneaux
            ],
            [
                'name' => 'Modélisation UML',
                'code' => 'GL202',
                'semester' => 1,
                'quota_cm' => 1320,
                'quota_td' => 660,
                'quota_tp' => 1320,
            ],
            [
                'name' => 'Systèmes de Base de Données',
                'code' => 'GL203',
                'semester' => 1,
                'quota_cm' => 1320,
                'quota_td' => 660,
                'quota_tp' => 1320,
            ],
            [
                'name' => 'Développement d\'Applications Web',
                'code' => 'GL204',
                'semester' => 1,
                'quota_cm' => 1320,
                'quota_td' => 660,
                'quota_tp' => 1320,
            ],
        ];

        $subjects = [];
        foreach ($subjectsData as $sData) {
            $totalQuota = $sData['quota_cm'] + $sData['quota_td'] + $sData['quota_tp'];
            $subject = Subject::create([
                'name' => $sData['name'],
                'code' => $sData['code'],
                'semester' => $sData['semester'],
                'quota_cm_minutes' => $sData['quota_cm'],
                'quota_td_minutes' => $sData['quota_td'],
                'quota_tp_minutes' => $sData['quota_tp'],
                'quota_total_remaining_minutes' => $totalQuota,
                'department_id' => $dept->id,
            ]);
            $subjects[$sData['code']] = $subject;
        }

        // 9. Affectations Professeur-Matière-Classe (subject_teacher)
        // Algorithmique (GL201)
        SubjectTeacher::create([
            'subject_id' => $subjects['GL201']->id,
            'teacher_id' => $teachers['nguena@iuc.cm']->id,
            'classe_id' => $classeJour->id,
            'type' => 'CM',
        ]);
        SubjectTeacher::create([
            'subject_id' => $subjects['GL201']->id,
            'teacher_id' => $teachers['talla@iuc.cm']->id,
            'classe_id' => $classeJour->id,
            'type' => 'TD',
        ]);
        SubjectTeacher::create([
            'subject_id' => $subjects['GL201']->id,
            'teacher_id' => $teachers['nguena@iuc.cm']->id,
            'classe_id' => $classeJour->id,
            'type' => 'TP',
        ]);

        // Modélisation UML (GL202)
        SubjectTeacher::create([
            'subject_id' => $subjects['GL202']->id,
            'teacher_id' => $teachers['tchinda@iuc.cm']->id,
            'classe_id' => $classeJour->id,
            'type' => 'CM',
        ]);
        SubjectTeacher::create([
            'subject_id' => $subjects['GL202']->id,
            'teacher_id' => $teachers['tchinda@iuc.cm']->id,
            'classe_id' => $classeJour->id,
            'type' => 'TD',
        ]);
        SubjectTeacher::create([
            'subject_id' => $subjects['GL202']->id,
            'teacher_id' => $teachers['tchinda@iuc.cm']->id,
            'classe_id' => $classeJour->id,
            'type' => 'TP',
        ]);

        // Base de Données (GL203)
        SubjectTeacher::create([
            'subject_id' => $subjects['GL203']->id,
            'teacher_id' => $teachers['kinfack@iuc.cm']->id,
            'classe_id' => $classeJour->id,
            'type' => 'CM',
        ]);
        SubjectTeacher::create([
            'subject_id' => $subjects['GL203']->id,
            'teacher_id' => $teachers['kinfack@iuc.cm']->id,
            'classe_id' => $classeJour->id,
            'type' => 'TD',
        ]);
        SubjectTeacher::create([
            'subject_id' => $subjects['GL203']->id,
            'teacher_id' => $teachers['kinfack@iuc.cm']->id,
            'classe_id' => $classeJour->id,
            'type' => 'TP',
        ]);

        // Affectations pour la classe du Soir (GLS3A)
        // Développement Web (GL204)
        SubjectTeacher::create([
            'subject_id' => $subjects['GL204']->id,
            'teacher_id' => $teachers['nguena@iuc.cm']->id,
            'classe_id' => $classeSoir->id,
            'type' => 'CM',
        ]);
        SubjectTeacher::create([
            'subject_id' => $subjects['GL204']->id,
            'teacher_id' => $teachers['talla@iuc.cm']->id,
            'classe_id' => $classeSoir->id,
            'type' => 'TD',
        ]);
        SubjectTeacher::create([
            'subject_id' => $subjects['GL204']->id,
            'teacher_id' => $teachers['nguena@iuc.cm']->id,
            'classe_id' => $classeSoir->id,
            'type' => 'TP',
        ]);

        // 10. Initialisation des Disponibilités Générales des Professeurs
        $slots = [
            '08:00 - 09:50',
            '10:10 - 12:00',
            '13:00 - 14:50',
            '15:10 - 17:00',
            '17:30 - 19:30',
            '20:00 - 21:30'
        ];
        $days = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'];

        // Dr Nguena : Disponible uniquement Lundi, Mardi, Mercredi (créneaux de journée 1-4)
        foreach (['lundi', 'mardi', 'mercredi'] as $day) {
            for ($i = 0; $i < 4; $i++) {
                TeacherAvailability::create([
                    'teacher_id' => $teachers['nguena@iuc.cm']->id,
                    'day_of_week' => $day,
                    'slot_number' => $slots[$i],
                    'is_available' => true,
                ]);
            }
        }
        
        // M. Talla : Disponible Mardi, Jeudi, Vendredi (toute la journée)
        foreach (['mardi', 'jeudi', 'vendredi'] as $day) {
            foreach ($slots as $slot) {
                TeacherAvailability::create([
                    'teacher_id' => $teachers['talla@iuc.cm']->id,
                    'day_of_week' => $day,
                    'slot_number' => $slot,
                    'is_available' => true,
                ]);
            }
        }

        // Mme Tchinda : Disponible Lundi au Samedi (créneaux de journée 1-4)
        foreach ($days as $day) {
            for ($i = 0; $i < 4; $i++) {
                TeacherAvailability::create([
                    'teacher_id' => $teachers['tchinda@iuc.cm']->id,
                    'day_of_week' => $day,
                    'slot_number' => $slots[$i],
                    'is_available' => true,
                ]);
            }
        }

        // M. Kinfack : Disponible Lundi, Mercredi, Vendredi (créneaux du soir 5-6)
        foreach (['lundi', 'mercredi', 'vendredi'] as $day) {
            for ($i = 4; $i < 6; $i++) {
                TeacherAvailability::create([
                    'teacher_id' => $teachers['kinfack@iuc.cm']->id,
                    'day_of_week' => $day,
                    'slot_number' => $slots[$i],
                    'is_available' => true,
                ]);
            }
        }

        // 11. Création de cinq Étudiants par défaut
        $studentsData = [
            [
                'name' => 'Fouda Christian',
                'email' => 'student1@iuc.cm',
                'phone' => '+237 600 000 002',
            ],
            [
                'name' => 'Ngassa Esther',
                'email' => 'student2@iuc.cm',
                'phone' => '+237 600 000 003',
            ],
            [
                'name' => 'professionnel Ismael',
                'email' => 'student3@iuc.cm',
                'phone' => '+237 600 000 004',
            ],
            [
                'name' => 'Colorado Gallaher',
                'email' => 'student4@iuc.cm',
                'phone' => '+237 600 000 005',
            ],
            [
                'name' => 'Mbe Njoum Solange',
                'email' => 'student5@iuc.cm',
                'phone' => '+237 600 000 006',
            ],
        ];

        $studentClassMap = [
            $classeJour->id,
            $classeJour->id,
            $classeSoir->id,
            $classeSoir->id,
            $classeJour->id,
        ];

        foreach ($studentsData as $index => $studentData) {
            $student = User::create([
                'name' => $studentData['name'],
                'email' => $studentData['email'],
                'password' => Hash::make('StudentPassword'),
                'department_id' => $dept->id,
                'classe_id' => $studentClassMap[$index] ?? $classeJour->id,
                'phone' => $studentData['phone'],
            ]);
            $student->assignRole($etudiantRole);
        }

        // 12. Générer les emplois du temps pour les classes créées (brouillon)
        try {
            $generator = new TimetableGenerator();
            $t1 = $generator->generateForClasse($classeJour->id);
            $t1->status = 'publie';
            $t1->save();

            $t2 = $generator->generateForClasse($classeSoir->id);
            $t2->status = 'publie';
            $t2->save();
        } catch (\Exception $e) {
            // Ne pas bloquer le seeding si la génération échoue en environnement de test
            // Log::error('Timetable generation failed: ' . $e->getMessage());
        }
    }
}
