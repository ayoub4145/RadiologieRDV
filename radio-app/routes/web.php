<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RendezVousController;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CreneauxController;
use Illuminate\Http\Request;
use Carbon\Carbon;

Route::get('/', function () {
    return view('index');
});


Route::get('/creneaux/{serviceId}', function(Request $request, $serviceId) {
    $isUrgent = (int) $request->query('urgent', 0); // 0 ou 1

    $creneaux = [];

    // Définir plage selon urgence
    $startDate = Carbon::now();
    $endDate = $isUrgent ? $startDate->copy()->addDay() : $startDate->copy()->addDays(60);

    $heureDebutJour = 9;
    $heureFinJour = 17;
    $dureeCreneauMinutes = 60;

    for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
        for ($heure = $heureDebutJour; $heure < $heureFinJour; $heure++) {
            $debut = $date->copy()->setHour($heure)->setMinute(0)->setSecond(0);
            $fin = $debut->copy()->addMinutes($dureeCreneauMinutes);

            // Ne proposer que les créneaux futurs (supérieurs à maintenant)
            if ($debut->lt(Carbon::now())) {
                continue;
            }

            $creneaux[] = [
                'id' => $date->format('Ymd') . $heure, // id unique généré
                'date' => $date->toDateString(),
                'time' => $debut->format('H:i'),
                'end_time' => $fin->format('H:i'),
            ];
        }
    }

    return response()->json($creneaux);
});



Route::get('/dashboard', [RendezVousController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {

    Route::get('/mes-rendezvous', [RendezVousController::class, 'mesRendezVous']);
    Route::delete('/annuler-rendezvous/{id}', [RendezVousController::class, 'annuler']);
    // Route::get('/creneaux/{service_id}', [CreneauxController::class, 'getByService']);
    Route::post('/prendre-rdv', [RendezVousController::class, 'store'])->name('rendezvous.store');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});

require __DIR__.'/auth.php';
