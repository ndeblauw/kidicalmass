<?php

namespace App\Livewire;

use App\Mail\ContactFormSubmitted;
use App\Models\ContactForm;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Validate;
use Livewire\Component;

/**
 * Start-a-group enquiry — the intake on /chapters/start-een-groep. Replaces the
 * old `mailto:bike@` "black hole" for would-be local organisers (D-12). One light
 * form serves both comfort levels via `path`: "praat eerst met iemand die het al
 * deed" (the team brokers a nearby trekker — no contact exposed) or "ik ben er
 * klaar voor" (the team reaches out). Either way the postcode + motivation give the
 * coordination team a real, triageable intent signal before a trekker's time is
 * spent. Mirrors PartnerEnquiry / ChapterVolunteerSignup; do not merge them.
 *
 * TODO (#37): land a dedicated StartGroupEnquiry model + a per-region routing rule
 * once the coordination team confirms ownership. For now the central comms inbox
 * receives every enquiry, tagged "Aanvraag nieuwe lokale groep" in the body.
 */
class StartGroupEnquiry extends Component
{
    /**
     * Comfort-level paths. Key = stored value; value = NL label.
     */
    public const PATH_OPTIONS = [
        'praten' => 'Ik praat eerst graag met iemand die het al deed',
        'klaar' => 'Ik ben er klaar voor, neem gerust contact op',
    ];

    /**
     * Core-team readiness (the high-intent signal). Key = stored value; value = NL label.
     */
    public const TEAM_OPTIONS = [
        'samen' => 'We zijn al met een paar',
        'interesse' => 'Een paar mensen tonen interesse',
        'alleen' => 'Voorlopig alleen ik',
    ];

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|email:rfc,dns,spoof|min:5|max:255')]
    public string $email = '';

    #[Validate('required|string|max:120')]
    public string $place = '';

    #[Validate('required|string|max:2000')]
    public string $motivation = '';

    #[Validate('nullable|in:samen,interesse,alleen')]
    public string $team = '';

    #[Validate('required|in:praten,klaar')]
    public string $path = '';

    #[Validate('max:0')]
    public string $website = '';

    public string $page_url = '';

    public bool $submitted = false;

    public string $confirmedName = '';

    public string $confirmedPath = '';

    public function mount(): void
    {
        $this->page_url = request()->url();
    }

    public function submit(): void
    {
        $this->validate();

        $this->confirmedName = explode(' ', trim($this->name))[0] ?? '';
        $this->confirmedPath = $this->path;

        // Honeypot tripped — fake success, send nothing.
        if (! empty($this->website)) {
            $this->submitted = true;
            $this->reset(['name', 'email', 'place', 'motivation', 'team', 'path', 'website']);

            return;
        }

        $body = "Aanvraag nieuwe lokale groep.\nGemeente / postcode: {$this->place}.";
        $body .= "\nWat wil deze persoon nu: ".self::PATH_OPTIONS[$this->path].'.';

        if ($this->team !== '' && isset(self::TEAM_OPTIONS[$this->team])) {
            $body .= "\nKernteam: ".self::TEAM_OPTIONS[$this->team].'.';
        }

        $body .= "\nWaarom: {$this->motivation}";

        $contactForm = ContactForm::create([
            'name' => $this->name,
            'email' => $this->email,
            'message' => $body,
            'phone' => null,
            'page_url' => $this->page_url,
            'honeypot' => $this->website ?: null,
        ]);

        try {
            Mail::to(config('kidicalmass.mail.communications'))
                ->send(new ContactFormSubmitted($contactForm));
        } catch (\Exception $e) {
            logger()->error('Failed to send start-group enquiry email: '.$e->getMessage());
        }

        $this->submitted = true;
        $this->reset(['name', 'email', 'place', 'motivation', 'team', 'path', 'website']);
    }

    public function render()
    {
        return view('livewire.start-group-enquiry', [
            'pathOptions' => self::PATH_OPTIONS,
            'teamOptions' => self::TEAM_OPTIONS,
        ]);
    }
}
