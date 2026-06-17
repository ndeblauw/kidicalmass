<?php

namespace App\Notifications\PinkVest;

use App\Models\Group;
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
        return (new MailMessage)
            ->subject('Welkom bij de roze hesjes van '.$this->group->name)
            ->line('hello')
            ->view('emails.pinkvest-welcome', [
                'firstName' => Str::before($notifiable->name, ' '),
                'group' => $this->group,
            ]);
    }
}
