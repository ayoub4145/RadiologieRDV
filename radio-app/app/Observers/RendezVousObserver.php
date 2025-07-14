<?php

namespace App\Observers;

use App\Models\RendezVous;
use App\Notifications\RendezVousUrgentNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;


class RendezVousObserver
{
    /**
     * Handle the RendezVous "created" event.
     */
    public function created(RendezVous $rendezVous): void
    {
     Log::info('Observer créé appelé pour RDV ID: '.$rendezVous->id);

    if ($rendezVous->is_urgent) {

        Log::info('RDV urgent détecté ID: '.$rendezVous->id);

        $rendezVous->load(['user', 'visiteur']);

        // Création d’un "destinataire fictif" pour l’admin
        $admin = new class {
            use \Illuminate\Notifications\Notifiable;
            public function routeNotificationForMail() {
                return 'mlouki.server@gmail.com';
            }
            public function routeNotificationForVonage() {
                return '212687520612'; // numéro SMS admin en format international
            }
        };

        Notification::send($admin, new RendezVousUrgentNotification($rendezVous));
    }

        // Si le rendez-vous est urgent, on envoie une notification
        if ($rendezVous->is_urgent) {
            Notification::send($rendezVous->visiteur, new RendezVousUrgentNotification($rendezVous));
            if ($rendezVous->user) {
                Notification::send($rendezVous->user, new RendezVousUrgentNotification($rendezVous));
            }
        }
    }

    /**
     * Handle the RendezVous "updated" event.
     */
    public function updated(RendezVous $rendezVous): void
    {
        //
    }

    /**
     * Handle the RendezVous "deleted" event.
     */
    public function deleted(RendezVous $rendezVous): void
    {
        //
    }

    /**
     * Handle the RendezVous "restored" event.
     */
    public function restored(RendezVous $rendezVous): void
    {
        //
    }

    /**
     * Handle the RendezVous "force deleted" event.
     */
    public function forceDeleted(RendezVous $rendezVous): void
    {
        //
    }
}
