<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\VonageMessage;
use Illuminate\Support\Facades\Log;
class RendezVousUrgentNotification extends Notification
{
    use Queueable;
    protected $rdv;

    /**
     * Create a new notification instance.
     */
    public function __construct($rdv)
    {
        $this->rdv = $rdv;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail','vonage'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('⚠️ Rendez-vous URGENT détecté')
            ->greeting('Bonjour')
            ->line('Un rendez-vous urgent a été enregistré :')
            ->line('Nom patient : ' . $this->getNomPatient())
            ->line('Téléphone : ' . $this->getTelephone())
            ->line('Date et heure : ' . $this->rdv->date_heure)
            ->line('Merci de traiter ce rendez-vous en priorité.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }

    public function toVonage($notifiable)
    {

 $message = '⚠️ RDV URGENT : ' . $this->getNomPatient() . ' - ' . $this->getTelephone() . ' le ' . $this->rdv->date_heure;
    $numero = $notifiable->routeNotificationForVonage();

    Log::info('=== ENVOI SMS ===');
    Log::info('Message: ' . $message);
    Log::info('Destinataire: ' . $numero);
    Log::info('Longueur message: ' . strlen($message));

    return (new VonageMessage)
        ->content($message);    }

    protected function getNomPatient()
    {
        if ($this->rdv->user) {
            return $this->rdv->user->name;
        } elseif ($this->rdv->visiteur) {
            return $this->rdv->visiteur->nom_visiteur;
        }
        return 'Inconnu';
    }

    protected function getTelephone()
    {
        if ($this->rdv->user) {
            return $this->rdv->user->phone_number;
        } elseif ($this->rdv->visiteur) {
            return $this->rdv->visiteur->telephone;
        }
        return '-';
    }
}
