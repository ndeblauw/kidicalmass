<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetLocale;
use App\Models\Group;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * 301s legacy Wix URLs to the new site (docs/wiki/design/26-redirect-map.md, D-7).
 *
 * Old Wix paths were language-neutral (NL/FR stacked on one page); new routes are
 * locale-prefixed. Per the D-7 rule the target locale resolves per request via
 * Accept-Language with NL as fallback — cookie/geo refinement only becomes
 * meaningful once SetLocale::SUPPORTED grows beyond nl.
 *
 * Routes pass either `target` (a route name, optionally with a `#fragment`) or
 * `zip` (a chapter postal code, resolved to the matching group at request time
 * so the map survives route-key or id changes).
 */
class LegacyRedirectController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $locale = $request->getPreferredLanguage(SetLocale::SUPPORTED) ?? SetLocale::SUPPORTED[0];

        // Read the map values off the route directly: scalar route parameters are
        // injected positionally, so a wildcard segment ({slug}) would land in the
        // wrong argument if these were method parameters.
        $target = $request->route()->parameter('target');
        $zip = $request->route()->parameter('zip');

        if ($zip !== null) {
            $group = Group::visible()->where('zip', $zip)->first();

            return $group
                ? redirect()->route('groups.show', ['locale' => $locale, 'group' => $group], 301)
                : redirect()->route('groups.index', ['locale' => $locale], 301);
        }

        [$route, $fragment] = array_pad(explode('#', $target, 2), 2, null);

        return redirect()->to(
            route($route, ['locale' => $locale]).($fragment !== null ? '#'.$fragment : ''),
            301
        );
    }
}
