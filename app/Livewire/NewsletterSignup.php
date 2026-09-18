<?php

namespace App\Livewire;

use App\Actions\SubscribeToNewsletterAction;
use Livewire\Component;
use Throwable;

class NewsletterSignup extends Component
{
    /**
     * RFC 5321 caps a full email address at 254 characters; anything longer is
     * a paste accident or junk, so we reject it with a friendly nudge.
     */
    private const EMAIL_RULES = 'required|email|max:254';

    public string $email = '';

    public bool $submitted = false;

    /**
     * Real-time feedback: when the visitor leaves the email field (wire:model.blur)
     * we normalise and validate the address straight away, so a typo is flagged
     * before they reach the submit button. An empty field stays quiet, though, so
     * we don't nag someone who simply tabbed past it.
     */
    public function updatedEmail(): void
    {
        $this->email = $this->normalizedEmail();

        if ($this->email === '') {
            $this->resetErrorBag('email');

            return;
        }

        $this->validateOnly('email', ['email' => self::EMAIL_RULES]);
    }

    public function subscribe(): void
    {
        $this->email = $this->normalizedEmail();
        $this->validate(['email' => self::EMAIL_RULES]);

        try {
            resolve(SubscribeToNewsletterAction::class)->handle($this->email, app()->getLocale());
        } catch (Throwable $exception) {
            report($exception);
            $this->addError('email', __('forms.newsletter.error'));

            return;
        }

        $this->submitted = true;
    }

    /**
     * Trimmed, lower-cased email. Livewire bypasses the HTTP TrimStrings
     * middleware, so a pasted "  Jouw@Email.BE " would otherwise fail an
     * exact-match check or create a near-duplicate subscription.
     */
    private function normalizedEmail(): string
    {
        return strtolower(trim($this->email));
    }

    public function render()
    {
        return view('livewire.newsletter-signup');
    }
}
