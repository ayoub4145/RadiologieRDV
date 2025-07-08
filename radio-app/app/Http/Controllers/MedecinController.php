<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MedecinController extends Controller
{
    public function index()
    {
        // Logique pour afficher le tableau de bord du médecin
        return view('medecin.dashboard');
    }
}
