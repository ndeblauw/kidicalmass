<?php

use App\Http\Middleware\SetLocale;
use Illuminate\Routing\Route;
use Illuminate\Support\Str;

if (! function_exists('alternate_locale_url')) {
    /**
     * URL of the current page in another supported locale, or null when the page
     * has no route in that locale. Backs the header language switch and the
     * <link rel="alternate" hreflang> tags.
     */
    function alternate_locale_url(string $locale, ?Route $route = null): ?string
    {
        if (! in_array($locale, SetLocale::SUPPORTED, true)) {
            return null;
        }

        $route ??= request()->route();

        if ($route?->getName() === null) {
            return null;
        }

        $name = $route->getName();
        $base = Str::startsWith($name, 'fr.') ? Str::after($name, 'fr.') : $name;
        $candidate = $locale === 'fr' ? 'fr.'.$base : $base;

        if (! app('router')->getRoutes()->hasNamedRoute($candidate)) {
            return null;
        }

        // Only the current request route carries bound values; an explicit route
        // passed for name-mapping (or the hreflang sweep) has none.
        $parameters = $route === request()->route()
            ? array_filter(
                $route->parameters(),
                fn (string $key): bool => $key !== 'locale',
                ARRAY_FILTER_USE_KEY,
            )
            : [];

        $url = localized_route($base, $parameters + ['locale' => $locale]);

        $query = request()->getQueryString();

        return $query !== null ? $url.'?'.$query : $url;
    }
}

if (! function_exists('localized_route')) {
    function localized_route(string $name, array $parameters = []): string
    {
        $locale = $parameters['locale'] ?? app()->getLocale();
        $localizedName = $locale === 'fr' ? 'fr.'.$name : $name;
        $route = app('router')->getRoutes()->getByName($localizedName);

        if ($route?->parameterNames() === [] || ! in_array('locale', $route->parameterNames(), true)) {
            unset($parameters['locale']);
        } else {
            $parameters['locale'] = $locale;
        }

        return route($localizedName, $parameters);
    }
}
