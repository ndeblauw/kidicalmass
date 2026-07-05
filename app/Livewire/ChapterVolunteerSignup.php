<?php

namespace App\Livewire;

use App\Mail\ContactFormSubmitted;
use App\Models\Activity;
use App\Models\ContactForm;
use App\Models\Group;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

/**
 * J2 — the chapter volunteer sign-up. Lives on the chapter page, so the enquiry is
 * routed to the chapter by context (no municipality dropdown). Distinct from the
 * generic VolunteerSignup used on the event-detail page; do not merge them.
 */
class ChapterVolunteerSignup extends Component
{
    /**
     * The role options offered. Key = value stored in the enquiry; value = NL label.
     */
    public const ROLE_OPTIONS = [
        'roze-hesje' => 'Roze hesje',
        'mede-organisator' => 'Mede-organisator',
        'communicator' => 'Communicator',
        'fotograaf' => 'Fotograaf',
        'dj' => 'DJ',
        'niet-zeker' => 'Nog niet zeker',
    ];

    /**
     * Short, concrete description per role so people can pick what fits without guessing.
     * Keyed by the same value as ROLE_OPTIONS.
     */
    public const ROLE_DESCRIPTIONS = [
        'roze-hesje' => 'Je rijdt mee als wegkapitein en houdt de groep veilig bij elk kruispunt.',
        'mede-organisator' => 'Je denkt mee over de route en helpt een rit op poten zetten.',
        'communicator' => 'Je houdt de buurt op de hoogte via socials, affiches en mond-tot-mond.',
        'fotograaf' => 'Je legt de leukste momenten van de rit vast.',
        'dj' => 'Je zorgt voor muziek en sfeer onderweg.',
        'niet-zeker' => 'Nog geen idee? Geen probleem, we zoeken samen iets dat bij je past.',
    ];

    public Group $group;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|email:rfc,dns,spoof|min:5|max:255')]
    public string $email = '';

    /** @var array<int, string> */
    #[Validate('array')]
    public array $roles = [];

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

    /**
     * This chapter's next upcoming ride, for the "come say hi" confirmation hook.
     */
    #[Computed]
    public function nextActivity(): ?Activity
    {
        return $this->group->activities()
            ->published()
            ->where('begin_date', '>=', now())
            ->orderBy('begin_date')
            ->first();
    }

    public function submit(): void
    {
        $this->validate();

        $this->confirmedName = explode(' ', trim($this->name))[0] ?? '';

        if (! empty($this->website)) {
            $this->submitted = true;
            $this->reset(['name', 'email', 'roles', 'message', 'website']);

            return;
        }

        $chosenRoles = collect($this->roles)
            ->filter(fn (string $role): bool => isset(self::ROLE_OPTIONS[$role]))
            ->map(fn (string $role): string => self::ROLE_OPTIONS[$role])
            ->join(', ');

        $body = "Aanmelding als vrijwilliger bij de lokale groep {$this->group->name}.";
        if ($chosenRoles !== '') {
            $body .= "\nInteresse: {$chosenRoles}.";
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
            'group_id' => $this->group->id,
        ]);

        try {
            // TODO (#37): route to this chapter's lead once a per-group lead email exists
            // on the Group model. For now the central comms inbox receives every enquiry,
            // tagged with the chapter name in the body above.
            Mail::to(config('kidicalmass.mail.communications'))
                ->send(new ContactFormSubmitted($contactForm));
        } catch (\Exception $e) {
            logger()->error('Failed to send chapter volunteer signup email: '.$e->getMessage());
        }

        $this->submitted = true;
        $this->reset(['name', 'email', 'roles', 'message', 'website']);
    }

    public function render()
    {
        return view('livewire.chapter-volunteer-signup', [
            'roleOptions' => self::ROLE_OPTIONS,
            'roleDescriptions' => self::ROLE_DESCRIPTIONS,
        ]);
    }
}
