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
