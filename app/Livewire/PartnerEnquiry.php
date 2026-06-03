<?php

namespace App\Livewire;

use App\Mail\ContactFormSubmitted;
use App\Models\ContactForm;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Validate;
use Livewire\Component;

/**
 * Become-a-partner enquiry (PAT-6). Replaces the old "mail us" black hole on
 * /about/partners: the prospect self-qualifies (org, type, formule of interest)
 * before contacting, and we receive a routed, tagged lead. The on-page summary +
 * downloadable Sponsorformules/Partnercharter PDFs do the pre-selling; this is the
 * warm hand-off. Mirrors ChapterVolunteerSignup; do not merge them.
 */
class PartnerEnquiry extends Component
{
    /**
     * Organisation types. Key = stored value; value = NL label.
     */
    public const TYPE_OPTIONS = [
        'vzw' => 'vzw / vereniging',
        'bedrijf' => 'Bedrijf',
        'overheid' => 'Gemeente / overheid',
        'andere' => 'Andere',
    ];

    /**
     * Formule interest (provisional — tiers from the Sponsorformules doc, pending
     * Leticia's national-scope confirmation). Key = stored value; value = NL label.
     */
    public const FORMULE_OPTIONS = [
        'supporter' => 'Supporter (vzw)',
        'partner' => 'Partner (vzw)',
        'community-partner' => 'Community Partner (vzw)',
        'friend' => 'Friend (bedrijf)',
        'sponsor' => 'Sponsor (bedrijf)',
        'main-partner' => 'Main Partner (bedrijf)',
        'nog-niet-zeker' => 'Nog niet zeker',
    ];

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|email:rfc,dns|min:5|max:255')]
    public string $email = '';

    #[Validate('required|string|max:255')]
    public string $organisation = '';

    #[Validate('required|in:vzw,bedrijf,overheid,andere')]
    public string $type = '';

    #[Validate('nullable|in:supporter,partner,community-partner,friend,sponsor,main-partner,nog-niet-zeker')]
    public string $formule = '';

    #[Validate('nullable|string|max:2000')]
    public string $message = '';

    #[Validate('max:0')]
    public string $website = '';

    public string $page_url = '';

    public bool $submitted = false;

    public string $confirmedName = '';

    public function mount(): void
    {
        $this->page_url = request()->url();
    }

    public function submit(): void
    {
        $this->validate();

        $this->confirmedName = explode(' ', trim($this->name))[0] ?? '';

        // Honeypot tripped — fake success, send nothing.
        if (! empty($this->website)) {
            $this->submitted = true;
            $this->reset(['name', 'email', 'organisation', 'type', 'formule', 'message', 'website']);

            return;
        }

        $typeLabel = self::TYPE_OPTIONS[$this->type] ?? $this->type;
        $body = "Aanvraag partnerschap.\nOrganisatie: {$this->organisation} ({$typeLabel}).";

        if ($this->formule !== '' && isset(self::FORMULE_OPTIONS[$this->formule])) {
            $body .= "\nInteresse in formule: ".self::FORMULE_OPTIONS[$this->formule].'.';
        }
        if ($this->message !== '') {
            $body .= "\nBericht: {$this->message}";
        }

        $contactForm = ContactForm::create([
            'name' => $this->name,
            'email' => $this->email,
            'message' => $body,
            'phone' => null,
            'page_url' => $this->page_url,
            'honeypot' => $this->website ?: null,
        ]);

        try {
            // TODO: route to a dedicated partnerships inbox once decided (bike@ vs
            // partners@ — open in about.md). For now the central comms inbox receives
            // every enquiry, tagged "Aanvraag partnerschap" in the body above.
            Mail::to(config('kidicalmass.mail.communications'))
                ->send(new ContactFormSubmitted($contactForm));
        } catch (\Exception $e) {
            logger()->error('Failed to send partner enquiry email: '.$e->getMessage());
        }

        $this->submitted = true;
        $this->reset(['name', 'email', 'organisation', 'type', 'formule', 'message', 'website']);
    }

    public function render()
    {
        return view('livewire.partner-enquiry', [
            'typeOptions' => self::TYPE_OPTIONS,
            'formuleOptions' => self::FORMULE_OPTIONS,
        ]);
    }
}
