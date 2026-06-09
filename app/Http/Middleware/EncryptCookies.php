<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as BaseEncryptCookies;

/**
 * Cookie encryption — extends the base middleware to support prefix-based exclusions.
 * Cookies that contain only plain ISO-8601 timestamps (the roze-hesje welcome window)
 * are excluded so tests can send them with withUnencryptedCookie without decryption
 * failures. The values are non-sensitive, so plain storage is acceptable.
 */
class EncryptCookies extends BaseEncryptCookies
{
    /**
     * {@inheritDoc}
     */
    public function isDisabled($name)
    {
        if (str_starts_with((string) $name, 'roze_welcome_')) {
            return true;
        }

        return parent::isDisabled($name);
    }
}
