{{--
    Over ons / Pers — /about/press (P-19)
    Restructured 2026-07 (spec: 2026-07-03-about-section-content-design.md,
    variant B): one contact section + the year-grouped PressArticle archive.
    Outlet strip and closing CTA cut (the archive shows the outlets; the page
    IS the contact). Copy: lang/nl/about.php (press_*). Structure only.
--}}
<x-layouts::site :title="__('nav.press')" :description="__('meta.press')">

    <x-page-hero
        :eyebrow="__('nav.press')"
        :title="__('about.press_title')">

    {{-- CONTACT — one section: pitch, background link, perscontact card --}}
    <section class="about-section about-section--wide">
        <div class="about-press">
            <div class="about-press__intro">
                <x-section-heading>{{ __('about.press_contact_title') }}</x-section-heading>
                <p>{{ __('about.press_contact_body') }}</p>
                <p class="about-section__link"><a href="{{ route('about.mission') }}">{{ __('about.press_background_link') }}</a></p>
            </div>
            <x-info-card :label="__('about.press_contact_label')">
                <a href="mailto:bike@kidicalmass.be" class="info-card__link">bike@kidicalmass.be</a>
                <p class="info-card__note">{{ __('about.press_contact_note') }}</p>
            </x-info-card>
        </div>
    </section>

    {{-- PERSOVERZICHT — year-grouped archive (PressArticle, admin-maintained) --}}
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
                                        <svg class="about-press__item-document-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                            <polyline points="14 2 14 8 20 8"/>
                                            <line x1="16" y1="13" x2="8" y2="13"/>
                                            <line x1="16" y1="17" x2="8" y2="17"/>
                                        </svg>
                                        {{ __('about.press_document_label') }}
                                    </a>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endforeach
            @else
                <div class="about-empty">
                    <h2 class="about-empty__title">{{ __('about.press_empty_title') }}</h2>
                    <p>{{ __('about.press_empty_body') }}</p>
                </div>
            @endif
        </div>
    </section>

    </x-page-hero>

</x-layouts::site>
