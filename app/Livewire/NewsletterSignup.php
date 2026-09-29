<?php

namespace App\Livewire;

use App\Actions\SubscribeToNewsletterAction;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;
use Throwable;

class NewsletterSignup extends Component
{
    /**
     * RFC 5321 caps a full email address at 254 characters; anything longer is
     * a paste accident or junk, so we reject it with a friendly nudge.
     * `filter` rejects dotless domains like "a@b" that RFC mode alone accepts
     * but MailerLite refuses; no `dns` check, so blur validation stays instant.
     */
    private const EMAIL_RULES = 'required|email:rfc,filter,spoof|max:254';

    /**
     * Signup attempts that reach MailerLite, per visitor IP and per address,
     * within one throttle window. Generous for a family retrying a typo, tight
     * enough that a script can't flood the list or burn the API quota.
     */
    public const MAX_ATTEMPTS_PER_IP = 5;

    public const MAX_ATTEMPTS_PER_EMAIL = 3;

    private const THROTTLE_WINDOW_SECONDS = 15 * 60;

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

        if ($this->isThrottled()) {
            $this->addError('email', __('forms.newsletter.throttled'));

            return;
        }

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
     * Counts this attempt against both the IP and the email limit, unless one of
     * them is already exhausted, in which case MailerLite must not be called.
     */
    private function isThrottled(): bool
    {
        $limits = [
            'newsletter-signup:ip:'.request()->ip() => self::MAX_ATTEMPTS_PER_IP,
            'newsletter-signup:email:'.sha1($this->email) => self::MAX_ATTEMPTS_PER_EMAIL,
        ];

        foreach ($limits as $key => $maxAttempts) {
            if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
                return true;
            }
        }

        foreach (array_keys($limits) as $key) {
            RateLimiter::hit($key, self::THROTTLE_WINDOW_SECONDS);
        }

        return false;
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
