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

test('blade components do not hardcode raw colors or px in styling contexts', function () {
    // Inherently raw-value components (SVG icons / logos / patterns).
    $allowlist = ['bike-icon', 'app-logo', 'app-logo-icon', 'placeholder-pattern'];

    $files = collect(File::allFiles(resource_path('views/components')))
        ->filter(fn ($f) => str_ends_with($f->getFilename(), '.blade.php'))
        // settings/ is the auth/profile UI, outside the public-site design system.
        ->reject(fn ($f) => str_starts_with(str_replace(DIRECTORY_SEPARATOR, '/', $f->getRelativePathname()), 'settings/'))
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
