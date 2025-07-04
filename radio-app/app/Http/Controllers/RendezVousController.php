<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class RendezVousController extends Controller

{
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'date' => 'required|date',
            'time' => 'required',
            'service_id' => 'required|exists:services,id',
        ]);

        // Create a new rendez-vous (appointment)
        $rendezVous = new \App\Models\RendezVous();
        $rendezVous->date = $request->input('date');
        $rendezVous->time = $request->input('time');
        $rendezVous->service_id = $request->input('service_id');
        $rendezVous->user_id = Auth::id(); // Assuming the user is authenticated

        // Save the rendez-vous
        $rendezVous->save();

        // Redirect or return a response
        return redirect()->route('dashboard')->with('success', 'Rendez-vous created successfully.');
    }
}
