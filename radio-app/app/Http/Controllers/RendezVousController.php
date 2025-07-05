<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\RendezVous;
use App\Models\Service;
class RendezVousController extends Controller

{
    public function store(Request $request)
{
    $validated = $request->validate([
        'service_id' => 'required|exists:services,id',
        'date_heure' => 'required|date|after:now',
        'commentaire' => 'nullable|string',
        'is_urgent' => 'nullable|boolean',
    ]);

    $rendezVous = new RendezVous();
    $rendezVous->user_id = Auth::id();
    $rendezVous->service_id = $validated['service_id'];
    $rendezVous->date_heure = $validated['date_heure'];
    $rendezVous->is_urgent = $request->has('is_urgent');
    $rendezVous->commentaire = $validated['commentaire'] ?? null;
    $rendezVous->statut = $request->has('is_urgent') ? 'en_attente' : 'confirmé';
    $rendezVous->save();

    return redirect()->back()->with('success', 'Votre rendez-vous a été enregistré avec succès.');
}
public function index()
{
    $services = Service::all(); // ou Service::orderBy('nom')->get();
    return view('dashboard', compact('services'));
}
}
