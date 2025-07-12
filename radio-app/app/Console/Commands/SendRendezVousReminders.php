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
        $now = Carbon::now();
        $nextDay = $now->copy()->addDay();

        // Find all appointments scheduled for the next day
        $rendezvous = RendezVous::whereDate('date_heure', $nextDay->toDateString())
            ->with(['user', 'visiteur', 'service'])
            ->get();

        $notificationsSent = 0;
        foreach ($rendezvous as $rdv) {
            if ($rdv->user) {
                $this->info("Sending notification to user ID {$rdv->user->id} for appointment ID {$rdv->id}");
                $rdv->user->notify(new RendezVousNotification($rdv));
                if ($rdv->visiteur && !empty($rdv->visiteur->email)) {
                    $this->info("Sending notification to visitor with email {$rdv->visiteur->email} for appointment ID {$rdv->id}");
                    $rdv->visiteur->notify(new RendezVousNotification($rdv));
                }
                $this->info("Notification sent to user ID {$rdv->user->id} for appointment ID {$rdv->id}");
                $notificationsSent++;
            }
            if ($rdv->visiteur && !empty($rdv->visiteur->email)) {
                $rdv->visiteur->notify(new RendezVousNotification($rdv));
                $this->info("Notification sent to visitor ID {$rdv->visiteur->id} for appointment ID {$rdv->id}");
                $notificationsSent++;
            }
        }

        $this->info("Reminders sent. Total notifications: {$notificationsSent}");
        return 0;
    }
}
