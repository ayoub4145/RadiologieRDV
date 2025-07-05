<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Creneaux;
use Illuminate\Support\Facades\Auth;
use App\Models\RendezVous;

class CreneauxController extends Controller
{
    public function index()
    {
        // Logic to fetch available time slots (creneaux)
        $creneaux = Creneaux::where('is_available', true)->get();

        return view('creneaux.index', compact('creneaux'));
    }
    public function reserver(Request $request)
    {
        $request->validate([
            'creneau_id' => 'required|exists:creneaux,id',
        ]);

        $creneau = Creneaux::findOrFail($request->creneau_id);

        if (!$creneau->is_available) {
            return back()->with('error', 'Ce créneau n\'est plus disponible.');
        }

        // Créer le rendez-vous
        RendezVous::create([
            'user_id' => Auth::id(),
            'service_id' => $creneau->service_id,
            'date_heure' => $creneau->date . ' ' . $creneau->time,
            'is_urgent' => false,
            'statut' => 'confirmé',
        ]);

        // Marquer le créneau comme réservé
        $creneau->update(['is_available' => false]);

        return redirect()->route('dashboard')->with('success', 'Rendez-vous confirmé.');    }
}
