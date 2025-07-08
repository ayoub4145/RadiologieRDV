<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        // Logique pour afficher le tableau de bord de l'administrateur
        return view('admin.dashboard');
    }
}
