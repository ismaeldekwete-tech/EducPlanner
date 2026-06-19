<?php

namespace App\Http\Controllers;

use App\Models\TimetableEntry;
use App\Services\TimetableGenerator;
use App\Services\TimetableRefusalHandler;
use Illuminate\Http\Request;

class TimetableController extends Controller
{
    /**
     * Déclenche la génération automatique de l'emploi du temps pour une classe.
     */
    public function generate(Request $request, TimetableGenerator $generator)
    {
        $request->validate(['classe_id' => 'required|uuid']);
        
        try {
            $generator->generateForClasse($request->classe_id);
            return back()->with('success', 'Emploi du temps généré avec succès en mode brouillon !');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur de génération : ' . $e->getMessage());
        }
    }

    /**
     * Traite le refus d'un enseignant et renvoie les résultats d'auto-remplacement.
     */
    public function handleRefusal(string $entryId, Request $request, TimetableRefusalHandler $refusalHandler)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);
        
        try {
            $replaced = $refusalHandler->handleRefusal($entryId, $request->reason);
            
            if ($replaced) {
                return back()->with('success', 'Le créneau a été automatiquement réaffecté à un autre cours de remplacement.');
            }
            
            return back()->with('info', 'Le créneau a été libéré (aucun cours alternatif éligible trouvé).');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors du traitement du refus : ' . $e->getMessage());
        }
    }
}
