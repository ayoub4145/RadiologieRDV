<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RendezVous;
use Carbon\Carbon;
use App\Notifications\RendezVousNotification;
class SendRendezVousReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-rendezvous-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envoyer un email de rappel pour les rendez-vous à venir dans 24 heures';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //Carbon::now() → récupère la date et l'heure actuelles.
        //addDay() → ajoute 1 jour (24 heures) à maintenant.
        $now = Carbon::now();
        $nextDay = $now->copy()->addDay();

        // Trouver les RDVs qui ont lieu entre maintenant+23h59 et maintenant+24h01
        // Cette requête cherche tous les rendez-vous planifiés exactement dans la minute qui correspond à “demain à la même heure”.
        // startOfMinute() et endOfMinute() encadrent précisément la minute pour éviter les doublons ou les oublis.

        $rendezvous = RendezVous::whereBetween('date_heure', [$nextDay->copy()->startOfMinute(), $nextDay->copy()->endOfMinute()])
            ->with(['user', 'visiteur', 'service'])
            ->get();
        //Pour chaque RDV récupéré :
//S'il a un user (compte connecté) → envoie une notification via Laravel Notifications.
//Sinon, s'il a un visiteur avec une adresse email → même chose.
        foreach ($rendezvous as $rdv) {
            if ($rdv->user) {
                $rdv->user->notify(new RendezVousNotification($rdv));
                $this->info("Notification envoyée à user ID {$rdv->user->id} pour RDV ID {$rdv->id}");
            } elseif ($rdv->visiteur && !empty($rdv->visiteur->email)) {
                $rdv->visiteur->notify(new RendezVousNotification($rdv));
                $this->info("Notification envoyée à visiteur ID {$rdv->visiteur->id} pour RDV ID {$rdv->id}");
            }
        }
       # Affiche un message dans le terminal pour indiquer que les notifications ont bien été envoyées.

        $this->info('Rappels envoyés.');
        return 0;
    }
}
