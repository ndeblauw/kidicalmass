<?php

namespace App\Livewire;

use App\Mail\ContactFormSubmitted;
use App\Models\ContactForm;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ContactFormComponent extends Component
{
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
    }

    public function submit(): void
    {
        $this->validate();

        // Check honeypot - if filled, it's likely spam
        if (! empty($this->website)) {
            // Pretend it was successful but don't save
            $this->submitted = true;
            $this->reset(['name', 'email', 'message', 'phone', 'website']);

            return;
        }

        // Save to database
        $contactForm = ContactForm::create([
            'name' => $this->name,
            'email' => $this->email,
            'message' => $this->message,
            'phone' => $this->phone ?: null,
            'page_url' => $this->page_url,
            'honeypot' => $this->website ?: null,
        ]);

        // Send email notification
        try {
            Mail::to(config('kidicalmass.email.communications'))
                ->send(new ContactFormSubmitted($contactForm));
        } catch (\Exception $e) {
            // Log error but don't fail the submission
            logger()->error('Failed to send contact form email: '.$e->getMessage());
        }

        $this->submitted = true;
        $this->reset(['name', 'email', 'message', 'phone', 'website']);
    }

    public function render()
    {
        return view('livewire.contact-form-component');
    }
}
