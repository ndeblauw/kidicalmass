<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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
     * Locale the visitor's browser asks for, falling back to the first supported
     * locale (Dutch). Used for the initial `/` redirect.
     */
    public static function detectFromRequest(Request $request): string
    {
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

        return $next($request);
    }
}
