<?php

namespace App\Actions;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use LogicException;

final class SubscribeToNewsletterAction
{
    /**
     * Add the subscriber to the locale group without removing other groups.
     */
    public function handle(string $email, string $locale): void
    {
        $groupIds = config('services.mailerlite.groups');
        $targetGroupId = is_array($groupIds) ? ($groupIds[$locale] ?? null) : null;

        if (! is_string($targetGroupId) || $targetGroupId === '') {
            throw new LogicException("No MailerLite group is configured for locale [{$locale}].");
        }

        $this->client()->post('/subscribers', [
            'email' => $email,
            'groups' => [$targetGroupId],
        ])->throw();
    }

    private function client(): PendingRequest
    {
        $token = config('services.mailerlite.token');
        $baseUrl = config('services.mailerlite.base_url');

        if (! is_string($token) || trim($token) === '') {
            throw new LogicException('The MailerLite API token is not configured.');
        }

        if (! is_string($baseUrl) || trim($baseUrl) === '') {
            throw new LogicException('The MailerLite API base URL is not configured.');
        }

        return Http::baseUrl(rtrim($baseUrl, '/'))
            ->acceptJson()
            ->asJson()
            ->withToken($token)
            ->connectTimeout(3)
            ->timeout(5);
    }
}
