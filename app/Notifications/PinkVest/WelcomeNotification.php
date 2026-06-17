<?php

namespace App\Notifications\PinkVest;

use App\Models\Group;
use App\Notifications\KidicalMassMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class WelcomeNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Group $group,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $firstName = Str::before($notifiable->name, ' ');

        return (new KidicalMassMessage)
            ->colour('pink')
            ->subject('Welkom bij de ROZE hesjes van '.$this->group->name)
            ->greeting('Welkom bij de roze hesjes!')
            ->line('Dag '.$firstName.',')
            ->line('Leuk dat je meefietst met Kidical Mass '.$this->group->name.'. Je hoort er nu officieel bij als roze hesje.')
            ->line('We hebben een plek voor je gemaakt met alles wat je nodig hebt: hoe een rit verloopt, wat je rol is, en wie er in je team zit. Voortaan op één plek terug te vinden, op je gsm én je laptop.')
            ->action('Check de info', '/login')
            ->salutation('Tot op de volgende rit!'."\n\n".'Het team van Kidical Mass '.$this->group->name);
    }
}
