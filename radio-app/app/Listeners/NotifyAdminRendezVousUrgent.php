<?php

namespace App\Listeners;

use App\Events\RendezVousCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\RendezVousUrgentNotification;
use Illuminate\Support\Facades\Log;

class NotifyAdminRendezVousUrgent
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(RendezVousCreated $event): void
{
     try {
        $rendezVous = $event->rendezVous;

        Log::info('=== DEBUG RENDEZ-VOUS URGENT ===');
        Log::info('RDV ID: ' . $rendezVous->id);
        Log::info('Is urgent (raw): ' . $rendezVous->is_urgent);
        Log::info('Is urgent (bool): ' . ($rendezVous->is_urgent ? 'TRUE' : 'FALSE'));
        Log::info('User ID: ' . $rendezVous->user_id);
        Log::info('Visiteur ID: ' . $rendezVous->visiteur_id);

        if ($rendezVous->is_urgent) {
            Log::info('RDV marqué comme urgent - chargement des relations');
            $rendezVous->load(['user', 'visiteur']);

            // Vérifiez les relations chargées
            Log::info('User chargé: ' . ($rendezVous->user ? $rendezVous->user->name : 'NULL'));
            Log::info('Visiteur chargé: ' . ($rendezVous->visiteur ? $rendezVous->visiteur->nom_visiteur : 'NULL'));

            $admin = new class {
                use \Illuminate\Notifications\Notifiable;

                public function routeNotificationForMail() {
                    return 'mlouki.server@gmail.com';
                }

                public function routeNotificationForVonage() {
                    Log::info('Route SMS appelée: +212687520612');
                    return '+212680475084'; // numéro SMS admin en format international
                }
            };

            Log::info('Envoi de la notification...');
            Notification::send($admin, new RendezVousUrgentNotification($rendezVous));
            Log::info('Notification envoyée sans exception');
        } else {
            Log::info('RDV non urgent - pas de notification');
        }

        Log::info('=== FIN DEBUG ===');

    } catch (\Exception $e) {
        Log::error('Erreur envoi notification : ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());
    }
}
}
