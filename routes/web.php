<?php

use App\Livewire\TimetableManager;
use App\Livewire\TeacherDashboard;
use App\Livewire\StudentDashboard;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route pour le login (gérée par Breeze)
require __DIR__.'/auth.php';

// Routes protégées
Route::middleware(['auth'])->group(function () {
    // Hub central /dashboard qui redirige selon le rôle
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->hasRole('SuperAdmin') || $user->hasRole('ChefDepartement')) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->hasRole('Professeur')) {
            return redirect()->route('teacher.dashboard');
        } elseif ($user->hasRole('Etudiant')) {
            return redirect()->route('student.dashboard');
        }
        abort(403, 'Rôle non autorisé.');
    })->name('dashboard');

    // Tableau de bord de l'administration
    Route::get('/admin/dashboard', TimetableManager::class)->name('admin.dashboard');

    // Page de profil utilisateur
    Route::get('/profile', function () {
        return view('profile');
    })->name('profile');

    // Tableau de bord des professeurs
    Route::get('/teacher/dashboard', TeacherDashboard::class)->name('teacher.dashboard');

    // Tableau de bord des étudiants
    Route::get('/student/dashboard', StudentDashboard::class)->name('student.dashboard');

    // Routes d'impression PDF
    Route::get('/student/print/{classeId}', [App\Http\Controllers\TimetablePrintController::class, 'studentPrint'])->name('student.print');
    Route::get('/timetable/print/{classeId}', [App\Http\Controllers\TimetablePrintController::class, 'studentPrint'])->name('timetable.print');
    Route::get('/teacher/print', [App\Http\Controllers\TimetablePrintController::class, 'teacherPrint'])->name('teacher.print');
    Route::get('/admin/print/{timetableId}', [App\Http\Controllers\TimetablePrintController::class, 'adminPrint'])->name('admin.print');
});

Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, ['fr', 'en'])) {
        session(['locale' => $locale]);
        if (auth()->check()) {
            auth()->user()->update(['lang' => $locale]);
        }
    }
    return redirect()->back();
})->name('lang.switch');

