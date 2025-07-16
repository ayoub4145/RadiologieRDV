<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RendezVousController;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CreneauxController;
use Illuminate\Http\Request;
use App\Http\Controllers\TwoFactorController;
use App\Models\RendezVous;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;
use App\Http\Controllers\MedecinController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\VisiteurController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\TypeInfoController;










Route::get('/test-notif-urgent', function () {
    $rendezVous = \App\Models\RendezVous::with(['user', 'visiteur'])->first();

    $admin = new class {
        use \Illuminate\Notifications\Notifiable;
        public function routeNotificationForMail() {
            return 'mlouki.server@gmail.com';
        }
        public function routeNotificationForVonage() {
            return '+212680475084'; // numéro SMS admin en format international
        }
    };

    \Illuminate\Support\Facades\Notification::send($admin, new \App\Notifications\RendezVousUrgentNotification($rendezVous));

    return 'Notification envoyée !';
});













// Page formulaire (GET)
Route::get('/rendez-vous-guest', [VisiteurController::class, 'index'])->name('visiteur.rendezvous.index');

// Soumission du formulaire (POST)
Route::post('/rendez-vous-guest', [VisiteurController::class, 'store'])->name('visiteur.rendezvous.store');

// Liste des rendez-vous pour un visiteur (GET via AJAX)
Route::get('/guest-mes-rendezvous', [VisiteurController::class, 'mesRendezVous'])->name('visiteur.rendezvous.mes');

// Annulation d’un rendez-vous visiteur (facultatif si tu le gères en JS)
Route::delete('/annuler-rendezvous/{id}', function ($id) {
    $rdv = \App\Models\RendezVous::whereNull('user_id')->findOrFail($id);
    $rdv->delete();
    return response()->json(['message' => 'Rendez-vous annulé']);
})->name('visiteur.rendezvous.annuler');


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




Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    // Autres routes d'administration ici
    //Routes pour index et dash admin :
    Route:: get('/show-sections-form',[SectionController::class,'create'])->name('sections.create');
// Route pour soumettre les données du formulaire de section (utilisée dans le dashboard)

    Route::post('/admin/sections/store', [AdminController::class, 'storeSectionData'])->name('admin.storeSectionData');
    // Route AJAX pour récupérer les infos dynamiques d'une section (utilisée dans le JS du dashboard)
    Route::get('/admin/sections/{sectionId}/type-infos', [SectionController::class, 'getTypeInfos']);
    Route::post('/admin/create-section',[SectionController::class,'store'])->name('sections.store');
    Route::get('/admin/search-section/{name}', [TypeInfoController::class, 'searchSection']);

    // 🔧 Modifier un champ (affiche le formulaire)
Route::get('/admin/sections/{section}/fields/{field}/edit', [TypeInfoController::class, 'editField'])->name('field.edit');

// 💾 Enregistrer la modification
Route::post('/admin/sections/{section}/update-field/{field}', [TypeInfoController::class, 'updateField'])->name('field.update');
    Route::delete('/admin/sections/{section}', [SectionController::class, 'destroy'])
        ->name('sections.destroy');
// 🗑 Supprimer un champ (remet sa valeur à null)
// Route::post('/admin/sections/{section}/delete-field/{field}', [TypeInfoController::class, 'deleteField'])->name('field.delete');

});

//Routes pour medecin
Route::middleware(['auth', 'role:medecin'])->group(function () {
    Route::get('/medecin/dashboard', [MedecinController::class, 'index']);
});
Route::get('/redirect-by-role', function () {
    $user = Auth::user();

    return match ($user->role) {
        'admin' => redirect('/admin/dashboard'),
        'medecin' => redirect('/medecin/dashboard'),
        default => redirect('/dashboard'), // patient
    };
})->middleware('auth')->name('redirect.by.role');
//authentification medecin
Route::get('/register/medecin', function () {
    return view('auth.register-medecin');
})->name('register.medecin.form');

Route::post('/register/medecin', [RegisteredUserController::class, 'storeMedecin'])->name('register.medecin');

// Routes 2FA - AVANT tout autre middleware
Route::middleware(['auth'])->prefix('2fa')->group(function () {
    Route::get('/setup', [TwoFactorController::class, 'show2faSetup'])->name('2fa.setup');
    Route::post('/enable', [TwoFactorController::class, 'enable'])->name('2fa.enable');
    Route::post('/disable', [TwoFactorController::class, 'disable'])->name('2fa.disable');

    Route::get('/verify', function () {
        return view('auth.2fa.verify');
    })->name('2fa.verify.form');

    Route::post('/verify', function (Request $request) {
        $request->validate(['code' => 'required|digits:6']);

        $user = Auth::user();
        $google2fa = app(Google2FA::class);

        if ($google2fa->verifyKey($user->google2fa_secret, $request->code)) {
            session(['2fa_passed' => true]);
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors(['code' => 'Code incorrect']);
    })->name('2fa.verify');
});
//Routes de DEBUG
Route::get('/debug-middleware', function () {
    dd(app('router')->getMiddleware());
});

Route::get('/hello', function () {
    return 'Hello world!';
});

Route::get('/', function () {
    return view('index');
});
/////////////////////////////////////////////////////////// Routes pour Patient
// Routes protégées par 2FA
Route::get('/dashboard', [RendezVousController::class, 'index'])
    ->middleware(['auth','verified','2fa'])
    ->name('dashboard');

Route::middleware(['auth','verified','2fa'])->group(function () {
    Route::get('/mes-rendezvous', [RendezVousController::class, 'mesRendezVous']);
    Route::delete('/annuler-rendezvous/{id}', [RendezVousController::class, 'annuler']);
    Route::post('/prendre-rdv', [RendezVousController::class, 'store'])->name('rendezvous.store');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
