<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Twilio\TwilioSmsMessage;
use App\Models\RendezVous;

class RendezVousNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $rendezVous;

    public function __construct($rendezVous)
    {
        $this->rendezVous = $rendezVous;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail','twilio'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $date = $this->rendezVous->date_heure->format('d/m/Y');
        $heure = $this->rendezVous->date_heure->format('H:i');
        $service = $this->rendezVous->service->service_name;

        return (new MailMessage)
                    ->subject('Rappel de votre rendez-vous dans 24h')
                    ->greeting('Bonjour ' . ($notifiable->name ?? ''))
                    ->line("Vous avez un rendez-vous prévu le $date à $heure.")
                    ->line("Service : $service")
                    ->line('Merci de bien vouloir vous préparer à temps.')
                    ->salutation('Cordialement,')
                    ->salutation(config('app.name'));
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
    public function toTwilio($notifiable)
    {
        return (new TwilioSmsMessage())
            ->content("Rappel : Vous avez un RDV le " . $this->rendezVous->date_heure->format('d/m/Y H:i') . " pour le service " . $this->rendezVous->service->service_name);
    }
}
