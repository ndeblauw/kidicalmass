<?php

namespace App\Livewire;

use App\Mail\ContactFormSubmitted;
use App\Models\ContactForm;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ContactFormComponent extends Component
{
    /** @var array<string, string> Topic buckets (national front door); label is prefixed into the stored message. */
    public const TOPICS = [
        'algemeen' => 'Algemene vraag',
        'pers' => 'Pers',
        'partnerschap' => 'Partnerschap of sponsoring',
    ];

    public string $topic = '';

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|email:rfc,dns,spoof|min:5|max:255')]
    public string $email = '';

    #[Validate('required|string|max:5000')]
    public string $message = '';

    #[Validate('nullable|string|max:255')]
    public string $phone = '';

    public string $page_url = '';

    #[Validate('max:0')] // Honeypot field - should remain empty
    public string $website = '';

    public bool $submitted = false;

    public function mount(): void
    {
        $this->page_url = request()->url();

        $requestedTopic = (string) request()->query('onderwerp', '');

        if (array_key_exists($requestedTopic, self::TOPICS)) {
            $this->topic = $requestedTopic;
        }
    }

    public function submit(): void
    {
        $this->validate();

        // The topic comes from a select; anything unexpected is silently dropped.
        if (! array_key_exists($this->topic, self::TOPICS)) {
            $this->topic = '';
        }

        // Check honeypot - if filled, it's likely spam
        if (! empty($this->website)) {
            // Pretend it was successful but don't save
            $this->submitted = true;
            $this->reset(['name', 'email', 'message', 'phone', 'website', 'topic']);

            return;
        }

        // Save to database
        $contactForm = ContactForm::create([
            'name' => $this->name,
            'email' => $this->email,
            'message' => ($this->topic !== '' ? 'Onderwerp: '.self::TOPICS[$this->topic]."\n\n" : '').$this->message,
            'phone' => $this->phone ?: null,
            'page_url' => $this->page_url,
            'honeypot' => $this->website ?: null,
        ]);

        // Send email notification
        try {
            Mail::to(config('kidicalmass.mail.communications'))
                ->send(new ContactFormSubmitted($contactForm));
        } catch (\Exception $e) {
            // Log error but don't fail the submission
            logger()->error('Failed to send contact form email: '.$e->getMessage());
        }

        $this->submitted = true;
        $this->reset(['name', 'email', 'message', 'phone', 'website', 'topic']);
    }

    public function render()
    {
        return view('livewire.contact-form-component');
    }
}
