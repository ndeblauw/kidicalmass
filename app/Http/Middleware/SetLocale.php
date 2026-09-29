<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /** @var list<string> */
    public const SUPPORTED = ['nl', 'fr'];

    /**
     * Locales shown in the language switch, in display order (client-requested).
     * May include announced locales that are not live yet: those render disabled
     * until they are added to SUPPORTED (and gain routes).
     *
     * @var list<string>
     */
    public const DISPLAY = ['fr', 'en', 'nl'];

    /**
     * Each locale's name in its own language, for the language menu.
     *
     * @var array<string, string>
     */
    public const NAMES = ['fr' => 'Français', 'en' => 'English', 'nl' => 'Nederlands'];

    /** Remembers the last language the visitor browsed in, for the `/` redirect. */
    public const COOKIE = 'locale';

    /**
     * Locale for the initial `/` redirect: the language the visitor last browsed
     * in, else the one their browser asks for, else the first supported (Dutch).
     */
    public static function detectFromRequest(Request $request): string
    {
        $remembered = $request->cookie(self::COOKIE);

        if (in_array($remembered, self::SUPPORTED, true)) {
            return $remembered;
        }

        return $request->getPreferredLanguage(self::SUPPORTED) ?? self::SUPPORTED[0];
    }

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->route('locale');

        if (! in_array($locale, self::SUPPORTED, true)) {
            abort(404);
        }

        app()->setLocale($locale);
        URL::defaults(['locale' => $locale]);

        if ($request->cookie(self::COOKIE) !== $locale) {
            Cookie::queue(self::COOKIE, $locale, 60 * 24 * 365);
        }

        return $next($request);
    }
}
