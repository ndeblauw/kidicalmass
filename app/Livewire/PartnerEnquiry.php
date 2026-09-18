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
     * Organisation types. Keys are the fixed stored codes; the labels are
     * translated per locale via lang/{nl,fr}/partners.php (page.enquiry.types).
     */
    public const TYPE_OPTIONS = ['asbl', 'association', 'entreprise', 'commune', 'pouvoir-public', 'autre'];

    /**
     * Formule interest (tiers from the Sponsorformules doc). Keys are the fixed
     * stored codes; the labels are translated per locale via
     * lang/{nl,fr}/partners.php (page.enquiry.formules).
     */
    public const FORMULE_OPTIONS = ['supporter', 'partner', 'community-partner', 'friend', 'sponsor', 'main-partner', 'nog-niet-zeker'];

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|email:rfc,dns|min:5|max:255')]
    public string $email = '';

    #[Validate('required|string|max:255')]
    public string $organisation = '';

    #[Validate('required|in:asbl,association,entreprise,commune,pouvoir-public,autre')]
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

        $typeLabel = __('partners.page.enquiry.types.'.$this->type);
        $body = "Aanvraag partnerschap.\nOrganisatie: {$this->organisation} ({$typeLabel}).";

        if ($this->formule !== '' && in_array($this->formule, self::FORMULE_OPTIONS, true)) {
            $body .= "\nInteresse in formule: ".__('partners.page.enquiry.formules.'.$this->formule).'.';
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
            'typeOptions' => collect(self::TYPE_OPTIONS)
                ->mapWithKeys(fn (string $code): array => [$code => __('partners.page.enquiry.types.'.$code)])
                ->all(),
            'formuleOptions' => collect(self::FORMULE_OPTIONS)
                ->mapWithKeys(fn (string $code): array => [$code => __('partners.page.enquiry.formules.'.$code)])
                ->all(),
        ]);
    }
}
