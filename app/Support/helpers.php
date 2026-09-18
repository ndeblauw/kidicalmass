<?php

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
