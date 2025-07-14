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
        try{
         $rendezVous = $event->rendezVous;

        if ($rendezVous->is_urgent) {
            $rendezVous->load(['user', 'visiteur']);

            $admin = new class {
                use \Illuminate\Notifications\Notifiable;

                public function routeNotificationForMail() {
                    return 'mlouki.server@gmail.com';
                }

                public function routeNotificationForVonage() {
                    return '212687520612'; // Numéro SMS admin
                }
            };

            Notification::send($admin, new RendezVousUrgentNotification($rendezVous));}
        } catch (\Exception $e) {
            Log::error('Erreur envoi SMS Vonage : '.$e->getMessage());

        }
    }
}
