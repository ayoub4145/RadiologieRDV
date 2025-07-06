<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RendezVousController;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CreneauxController;

Route::get('/', function () {
    return view('index');
});

Route::get('/dashboard', [RendezVousController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {

    // Route::get('/creneaux', [CreneauxController::class, 'index'])->name('creneaux.index');
    // Route::get('/creneaux-disponibles', [CreneauxController::class, 'disponibles'])->name('creneaux.disponibles');
    // Route::post('/rendezvous/reserver', [CreneauxController::class, 'reserver'])->name('creneaux.reserver');

    Route::post('/prendre-rdv', [RendezVousController::class, 'store'])->name('rendezvous.store');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
