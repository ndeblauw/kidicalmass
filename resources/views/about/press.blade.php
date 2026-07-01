{{--
    Over ons / Pers — /about/press (P-19)
    Colour story: blue → white → light-blue → white. Structure only.
--}}
<x-layouts::site title="Pers">

    <x-page-hero
        eyebrow="Pers"
        title="Het verhaal van de beweging.">

    {{-- INTRO + CONTACT --}}
    <section class="about-section about-section--wide">
        <div class="about-press">
            <div class="about-press__intro">
                <x-section-heading>Journalisten, we praten graag</x-section-heading>
                <p>We brengen je in contact met lokale trekkers, delen cijfers, regelen een fotomoment bij een volgende fietsparade of geven achtergrond bij de beweging.</p>
                <ul class="about-press__offer" role="list">
                    <li>Contact met lokale afdelingen en gezinnen</li>
                    <li>Cijfers en achtergrond over de beweging</li>
                    <li>Een fotomoment bij een aankomende rit</li>
                </ul>
            </div>
            <x-info-card label="Perscontact">
                <a href="mailto:bike@kidicalmass.be" class="info-card__link">bike@kidicalmass.be</a>
                <p class="info-card__note">We antwoorden zo snel als vrijwilligers dat kunnen.</p>
            </x-info-card>
        </div>

        {{-- Eerder in de media --}}
        <div class="about-press__outlets">
            <span class="about-press__outlets-label">Eerder verschenen in</span>
            <ul role="list">
                <li>RTBF</li>
                <li>BX1</li>
                <li>BRUZZ</li>
                <li>La DH</li>
                <li>HLN</li>
                <li>Het Nieuwsblad</li>
            </ul>
        </div>
    </section>

    {{-- PERSOVERZICHT --}}
    <section class="about-band about-band--light-blue">
        <div class="container mx-auto px-4">
            @if ($articlesByYear->isNotEmpty())
                @foreach ($articlesByYear as $year => $articles)
                    <h2 class="about-press__year">{{ $year }}</h2>
                    <ul class="about-press__list" role="list">
                        @foreach ($articles as $article)
                            <li class="about-press__item">
                                <span class="about-press__item-outlet">{{ $article->outlet }}</span>
                                <span class="about-press__item-date">— {{ $article->published_at->isoFormat('D MMMM YYYY') }}</span>
                                @if ($article->url)
                                    <a href="{{ $article->url }}" target="_blank" rel="noopener noreferrer" class="about-press__item-title">
                                @else
                                    <span class="about-press__item-title">
                                @endif
                                    {{ $article->title }}
                                @if ($article->url)
                                    </a>
                                @else
                                    </span>
                                @endif
                                @if ($article->getFirstMedia('document'))
                                    <a href="{{ $article->getFirstMediaUrl('document') }}" target="_blank" class="about-press__item-document" rel="noopener noreferrer">
                                        {{-- PDF / scan indicator --}}
                                        <svg class="about-press__item-document-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                            <polyline points="14 2 14 8 20 8"/>
                                            <line x1="16" y1="13" x2="8" y2="13"/>
                                            <line x1="16" y1="17" x2="8" y2="17"/>
                                        </svg>
                                        Artikel
                                    </a>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endforeach
            @else
                <div class="about-empty">
                    <h2 class="about-empty__title">We bouwen aan een persoverzicht</h2>
                    <p>Kidical Mass kwam de afgelopen jaren in heel wat kranten, radio en tv. We brengen die berichtgeving binnenkort samen op één plek. Schreef je over Kidical Mass en wil je dat je artikel hier verschijnt? Laat het ons weten via <a href="mailto:bike@kidicalmass.be">bike@kidicalmass.be</a>.</p>
                </div>
            @endif
        </div>
    </section>

    {{-- ACHTERGROND --}}
    <section class="about-section">
        <p class="about-section__link"><a href="{{ route('about.mission') }}">Achtergrond en cijfers: lees onze missie →</a></p>
    </section>

    </x-page-hero>

    <x-slot:closing>
        <x-closing-cta heading="Vragen van de pers?"
            :href="route('contact')" label="Neem contact op" />
    </x-slot:closing>

</x-layouts::site>
