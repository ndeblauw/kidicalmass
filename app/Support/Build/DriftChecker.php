<?php

namespace App\Support\Build;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

class DriftChecker
{
    /** @param string[] $stubMarkers */
    public function __construct(
        private int $stubLineThreshold,
        private array $stubMarkers,
    ) {}

    /**
     * Compares declared registry status against the real code/files and returns
     * mechanical mismatches. Only mechanical facts (file exists / is-stub /
     * route registered) — descriptive drift is caught by the staleness signal.
     *
     * @param  array<int,array>  $pages
     * @param  array<int,array>  $patterns
     * @return array<int,array{id:string,message:string}>
     */
    public function check(array $pages, array $patterns): array
    {
        $findings = [];
        $routePaths = $this->routePaths();

        foreach ($pages as $p) {
            if ($p['stages']['ux'] !== Stage::NotStarted && $p['briefPath'] && ! File::exists(base_path($p['briefPath']))) {
                $findings[] = ['id' => $p['id'], 'message' => "{$p['id']}: UX gemarkeerd maar briefing {$p['briefPath']} ontbreekt."];
            }
            if ($p['stages']['wireframe'] !== Stage::NotStarted && $this->viewIsStub($p['viewPath'] ?? null)) {
                $findings[] = ['id' => $p['id'], 'message' => "{$p['id']}: Wireframe gemarkeerd maar view lijkt nog een stub."];
            }
            // Only flag a missing route when the page claims to be wireframed —
            // an all-🔴 unbuilt page legitimately has no route yet.
            if ($p['stages']['wireframe'] !== Stage::NotStarted && ! $this->routeExists($p['routeUri'] ?? null, $routePaths)) {
                $findings[] = ['id' => $p['id'], 'message' => "{$p['id']}: route ".($p['routeUri'] ?? $p['slug']).' niet geregistreerd.'];
            }
        }

        foreach ($patterns as $pat) {
            if (! empty($pat['partialPath']) && ! File::exists(base_path($pat['partialPath']))) {
                $findings[] = ['id' => $pat['id'], 'message' => "{$pat['id']}: partial {$pat['partialPath']} geclaimd maar ontbreekt."];
            }
        }

        return $findings;
    }

    private function viewIsStub(?string $viewPath): bool
    {
        if (! $viewPath) {
            return false; // unknown view → don't flag
        }
        $full = base_path($viewPath);
        if (! File::exists($full)) {
            return true;
        }
        $content = File::get($full);
        if (substr_count($content, "\n") + 1 < $this->stubLineThreshold) {
            return true;
        }
        foreach ($this->stubMarkers as $marker) {
            if (str_contains($content, $marker)) {
                return true;
            }
        }

        return false;
    }

    /** @param string[] $routePaths */
    private function routeExists(?string $routeUri, array $routePaths): bool
    {
        if ($routeUri === null) {
            return false; // claimed built but no route mapped → drift
        }

        return in_array(trim($routeUri, '/') ?: '/', $routePaths, true);
    }

    /**
     * Registered route URIs, normalised. Public routes carry a `{locale}` prefix
     * (see routes/web.php); we add the prefix-stripped form too so the registry's
     * locale-agnostic slugs (`events`, `about/news`) resolve.
     *
     * @return string[]
     */
    private function routePaths(): array
    {
        $paths = [];
        foreach (Route::getRoutes() as $r) {
            $uri = trim($r->uri(), '/') ?: '/';
            $paths[] = $uri;
            if ($uri === '{locale}') {
                $paths[] = '/';
            } elseif (str_starts_with($uri, '{locale}/')) {
                $paths[] = substr($uri, strlen('{locale}/'));
            }
        }

        return array_values(array_unique($paths));
    }
}
