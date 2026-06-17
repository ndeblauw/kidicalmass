<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

class KidicalMassMessage extends MailMessage
{
    public ?string $colour = null;

    public function __construct()
    {
        $this->view = 'emails.pinkvest-notification';
    }

    public function colour(?string $colour): static
    {
        $this->colour = $colour;

        return $this;
    }

    public function data(): array
    {
        return array_merge(parent::data(), [
            'colour' => $this->colour,
        ]);
    }
}
