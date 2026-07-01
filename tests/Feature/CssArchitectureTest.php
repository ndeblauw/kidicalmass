<?php

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

/**
 * Enforces the role-based CSS partials architecture.
 * See docs/superpowers/specs/2026-06-06-css-partials-architecture-design.md
 */
function cssPartials(): Collection
{
    return collect(File::allFiles(resource_path('css')))
        ->filter(fn ($f) => $f->getExtension() === 'css')
        ->reject(fn ($f) => $f->getFilename() === 'app.css');
}

test('every css partial is imported by app.css', function () {
    $appCss = File::get(resource_path('css/app.css'));

    foreach (cssPartials() as $partial) {
        $relative = './'.str_replace(resource_path('css').DIRECTORY_SEPARATOR, '', $partial->getPathname());
        $relative = str_replace(DIRECTORY_SEPARATOR, '/', $relative);
        expect($appCss)->toContain("@import '{$relative}'");
    }
});

test('every local @import in app.css resolves to an existing file', function () {
    $appCss = File::get(resource_path('css/app.css'));
    preg_match_all("/@import\s+'(\.\/[^']+)'/", $appCss, $matches);

    expect($matches[1])->not->toBeEmpty();

    foreach ($matches[1] as $importPath) {
        $abs = resource_path('css/'.ltrim($importPath, './'));
        expect(File::exists($abs))->toBeTrue("Dangling @import in app.css: {$importPath}");
    }
});

test('body clips horizontal overflow so full-bleed 100vw bands cannot cause a phantom scrollbar', function () {
    // Full-bleed bands site-wide use `width: 100vw; margin: calc(50% - 50vw)`. On systems with
    // classic (space-consuming) scrollbars, 100vw exceeds the content box by the scrollbar width,
    // producing a horizontal scrollbar on every page. The global guard is `body { overflow-x: clip }`
    // — `clip`, not `hidden`, so sticky/pinned heroes are unaffected.
    $appCss = File::get(resource_path('css/app.css'));

    expect($appCss)->toMatch('/body\s*\{[^}]*overflow-x:\s*clip;[^}]*\}/');
});

test('css partials do not hardcode raw hex or ink/white rgb literals', function () {
    // Colours in partials must come from tokens or color-mix() — never raw hex
    // or the ink/white values spelled out as rgb(). px is allowed (borders, radii,
    // shadow geometry) since there are no border/radius spacing tokens yet.
    $violations = [];

    foreach (cssPartials() as $partial) {
        $content = File::get($partial->getPathname());

        preg_match_all('/#[0-9a-fA-F]{3,8}\b/', $content, $hex);
        // The ink (#281a39) and white spelled out as rgb()/rgba() — space- or
        // comma-separated — bypass the hex check.
        preg_match_all('/rgba?\(\s*40[\s,]+26[\s,]+57\b[^)]*\)/i', $content, $ink);
        preg_match_all('/rgba?\(\s*255[\s,]+255[\s,]+255\b[^)]*\)/i', $content, $white);

        $hits = array_merge($hex[0], $ink[0], $white[0]);
        if ($hits !== []) {
            $violations[$partial->getFilename()] = array_values(array_unique($hits));
        }
    }

    expect($violations)->toBe(
        [],
        'Raw colour literals in CSS partials (use a token or color-mix): '.json_encode($violations, JSON_PRETTY_PRINT)
    );
});

test('every css partial wraps its rules in an @layer', function () {
    // An unlayered partial wins the cascade over every @layer components partial
    // regardless of source order — a silent specificity footgun.
    $violations = [];

    foreach (cssPartials() as $partial) {
        if (! str_contains(File::get($partial->getPathname()), '@layer')) {
            $violations[] = $partial->getFilename();
        }
    }

    expect($violations)->toBe(
        [],
        'CSS partials missing an @layer wrapper: '.json_encode($violations, JSON_PRETTY_PRINT)
    );
});

test('blade components do not hardcode raw colors or px in styling contexts', function () {
    // Inherently raw-value components (SVG icons / logos / patterns).
    $allowlist = ['bike-icon', 'app-logo', 'app-logo-icon', 'placeholder-pattern'];

    $files = collect(File::allFiles(resource_path('views/components')))
        ->filter(fn ($f) => str_ends_with($f->getFilename(), '.blade.php'))
        // settings/ is the auth/profile UI, outside the public-site design system.
        ->reject(fn ($f) => str_starts_with(str_replace(DIRECTORY_SEPARATOR, '/', $f->getRelativePathname()), 'settings/'))
        // emails/ components use inline styles with raw values — required by email clients.
        ->reject(fn ($f) => str_starts_with(str_replace(DIRECTORY_SEPARATOR, '/', $f->getRelativePathname()), 'emails/'))
        ->reject(fn ($f) => in_array(str_replace('.blade.php', '', $f->getFilename()), $allowlist, true));

    $raw = '#[0-9a-fA-F]{3,8}\b|\b\d+px\b';
    $violations = [];

    foreach ($files as $file) {
        $content = File::get($file->getPathname());

        // Tailwind arbitrary values: class="… [color:#fff] min-h-[60px] …"
        preg_match_all('/\[[^\]\s]*(?:'.$raw.')[^\]]*\]/', $content, $arbitrary);
        // Inline style declarations: style="color:#fff; width:12px"
        preg_match_all('/style\s*=\s*"[^"]*(?:'.$raw.')[^"]*"/', $content, $inline);

        $hits = array_merge($arbitrary[0], $inline[0]);
        if ($hits !== []) {
            $violations[$file->getRelativePathname()] = array_values(array_unique($hits));
        }
    }

    expect($violations)->toBe(
        [],
        'Raw hex/px in components (use tokens, or add to allowlist if SVG/icon): '.json_encode($violations, JSON_PRETTY_PRINT)
    );
});
