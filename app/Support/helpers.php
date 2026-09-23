<?php

use App\Http\Middleware\SetLocale;
use Illuminate\Routing\Route;
use Illuminate\Support\Str;

if (! function_exists('locale_route_name')) {
    /**
     * Fully qualified route name for a locale. The default locale (nl) keeps the
     * bare name; every other supported locale is prefixed with "<locale>."
     * (fr.home, and en.home once English routes exist). The prefix scheme lives
     * here alone, so adding a locale to SetLocale::SUPPORTED is all that is needed.
     */
    function locale_route_name(string $name, string $locale): string
    {
        return $locale === SetLocale::SUPPORTED[0] ? $name : $locale.'.'.$name;
    }
}

if (! function_exists('route_base_name')) {
    /**
     * Route name with its locale prefix stripped, so a route can be compared
     * regardless of the request locale (home, fr.home and en.home all yield
     * "home"). Used by nav highlighting and language mapping.
     */
    function route_base_name(?string $name): ?string
    {
        if ($name === null) {
            return null;
        }

        foreach (SetLocale::SUPPORTED as $locale) {
            if ($locale !== SetLocale::SUPPORTED[0] && Str::startsWith($name, $locale.'.')) {
                return Str::after($name, $locale.'.');
            }
        }

        return $name;
    }
}

if (! function_exists('route_is')) {
    /**
     * Locale-aware request()->routeIs(): matches the patterns against the route
     * name with its locale prefix stripped, so "activities.*" also matches
     * fr.activities.show and en.activities.show.
     */
    function route_is(string ...$patterns): bool
    {
        $base = route_base_name(request()->route()?->getName());

        return $base !== null && Str::is($patterns, $base);
    }
}

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
        $base = route_base_name($name) ?? $name;
        $candidate = locale_route_name($base, $locale);

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
        $localizedName = locale_route_name($name, $locale);
        $route = app('router')->getRoutes()->getByName($localizedName);

        if ($route?->parameterNames() === [] || ! in_array('locale', $route->parameterNames(), true)) {
            unset($parameters['locale']);
        } else {
            $parameters['locale'] = $locale;
        }

        return route($localizedName, $parameters);
    }
}
