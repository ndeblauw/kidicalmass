<?php

namespace App\Livewire;

use App\Mail\ContactFormSubmitted;
use App\Models\ContactForm;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Validate;
use Livewire\Component;

class VolunteerSignup extends Component
{
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|email:rfc,dns,spoof|min:5|max:255')]
    public string $email = '';

    #[Validate('max:0')]
    public string $website = '';

    public string $page_url = '';

    public bool $submitted = false;

    public function mount(): void
    {
        $this->page_url = request()->url();
    }

    public function submit(): void
    {
        $this->validate();

        if (! empty($this->website)) {
            $this->submitted = true;
            $this->reset(['name', 'email', 'website']);

            return;
        }

        $contactForm = ContactForm::create([
            'name' => $this->name,
            'email' => $this->email,
            'message' => 'Aanmelding als roze hesje.',
            'phone' => null,
            'page_url' => $this->page_url,
            'honeypot' => $this->website ?: null,
        ]);

        try {
            Mail::to(config('kidicalmass.email.communications'))
                ->send(new ContactFormSubmitted($contactForm));
        } catch (\Exception $e) {
            logger()->error('Failed to send volunteer signup email: '.$e->getMessage());
        }

        $this->submitted = true;
        $this->reset(['name', 'email', 'website']);
    }

    public function render()
    {
        return view('livewire.volunteer-signup');
    }
}
