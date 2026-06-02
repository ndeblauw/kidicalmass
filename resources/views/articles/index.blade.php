{{--
    Over ons / Nieuws — /about/news (P-18)
    NL surface pass 2026-06-03. Editorial hub, low volume: the empty state must look
    intentional, not broken (DESIGN.md / about-content.md). Reuses x-article-card.
    Plan: docs/wiki/design/30-skeleton/about.md + about-journey.md
--}}
<x-layouts::site title="Nieuws">
    <div class="mx-auto max-w-5xl space-y-10">
        <header class="space-y-3">
            <h1>Nieuws</h1>
            <p class="about-feed__lead">Updates van de beweging: nieuwe afdelingen, mijlpalen en wat we onderweg leren.</p>
        </header>

        @if ($articles->isNotEmpty())
            <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($articles as $article)
                    <x-article-card :article="$article" />
                @endforeach
            </div>

            <div>{{ $articles->links() }}</div>
        @else
            <div class="about-empty">
                <h2 class="about-empty__title">Nog niets te zien</h2>
                <p>We zijn nog maar net begonnen. Kom binnenkort terug, of volg ons op
                    <a href="https://www.instagram.com/kidicalmass.belgium/" target="_blank" rel="noopener noreferrer">Instagram</a>
                    en <a href="https://www.facebook.com/Kidicalmass.brussels" target="_blank" rel="noopener noreferrer">Facebook</a>
                    voor updates zodra ze er zijn.</p>
            </div>
        @endif
    </div>
</x-layouts::site>
