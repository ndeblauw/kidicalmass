<?php

namespace App\Mail;

use App\Models\Group;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

/**
 * Invite a new pink-vest volunteer to activate their account (prototype).
 * The CTA points at the activation/set-password step for their chapter.
 */
class VolunteerInvite extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $volunteer,
        public Group $group,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welkom bij de roze hesjes van '.$this->group->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.volunteer-invite',
            with: [
                'firstName' => Str::before($this->volunteer->name, ' '),
                'group' => $this->group,
                'activateUrl' => route('backstage.activate', $this->group),
            ],
        );
    }
}
