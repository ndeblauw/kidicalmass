{{--
    Nieuwsdetail — /about/news/{article}. Normalisatie-pas 2026-07 (design-
    choices-nieuws, keuze B + B2 + B2B): rit-frame hero (blauwe band, eyebrow
    als teruglink, hoofdfoto zakt als gekantelde kaart het paneel in), body
    links uitgelijnd met een sticky rail rechts (hairline-lijst Meer nieuws +
    nieuwsbrief-optin). Zonder hoofdfoto valt de hero terug op de kale band.
--}}
@php($mainMedia = $article->getFirstMedia('main'))
@php($date = $article->published_at ?? $article->created_at)
@php($neighbours = collect([$newerArticle, $olderArticle])->filter())

<x-layouts::site title="{{ $article->title_nl }}" :description="$article->metaDescription()" :og-image="$article->ogImageUrl()" og-type="article">

    <x-page-hero
        :eyebrow="__('nav.news')"
        :eyebrow-href="route('articles.index')"
        :title="$article->title_nl"
        size="compact"
        :photo-url="$mainMedia?->getUrl()"
        :photo-srcset="$mainMedia?->getSrcset() ?: null"
        :photo-alt="$article->title_nl"
        :photo-tilt="$mainMedia !== null">

        <x-slot:lead>
            <p class="article-hero__meta">
                @if ($article->author){{ $article->author->name }} · @endif
                <time datetime="{{ $date->format('Y-m-d') }}">{{ $date->isoFormat('D MMMM YYYY') }}</time>
            </p>
            @if ($article->groups->isNotEmpty())
                <div class="article-hero__chips">
                    @foreach ($article->groups as $group)
                        <a href="{{ route('groups.show', $group) }}" class="link-plain article-hero__chip">{{ $group->name }}</a>
                    @endforeach
                </div>
            @endif
        </x-slot:lead>

        <div class="grid gap-10 pt-6 lg:grid-cols-[minmax(0,1fr)_20rem] lg:gap-16 lg:pt-16">
            <article class="max-w-3xl space-y-8">
                <div class="article-body">
                    {!! $article->content_html !!}
                </div>

                @if ($article->getMedia('gallery')->count() > 0)
                    <section class="space-y-4" aria-label="{{ __('about.news_gallery') }}">
                        <div class="grid gap-4 sm:grid-cols-2">
                            @foreach ($article->getMedia('gallery') as $media)
                                <div class="aspect-[4/3] overflow-hidden rounded-xl">
                                    {{-- Alt: an admin-set "alt" custom property when present; never
                                         the article title repeated N times (a11y). --}}
                                    <img src="{{ $media->getUrl() }}" @if ($media->getSrcset()) srcset="{{ $media->getSrcset() }}" sizes="(min-width: 640px) 50vw, 100vw" @endif alt="{{ $media->getCustomProperty('alt', '') }}" class="h-full w-full object-cover" loading="lazy" decoding="async">
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif
            </article>

            <aside class="flex flex-col gap-8 lg:sticky lg:top-28 lg:self-start">
                @if ($neighbours->isNotEmpty())
                    <nav aria-label="{{ __('about.news_more_title') }}" data-article-neighbours>
                        <h2 class="article-rail__label">{{ __('about.news_more_title') }}</h2>
                        <ul role="list" class="article-rail__list">
                            @foreach ($neighbours as $neighbour)
                                <li>
                                    <a href="{{ route('articles.show', $neighbour) }}" class="link-plain article-rail__item">
                                        <span class="article-rail__item-title">{{ $neighbour->title_nl }}</span>
                                        <span class="article-rail__item-date"><time datetime="{{ ($neighbour->published_at ?? $neighbour->created_at)->format('Y-m-d') }}">{{ ($neighbour->published_at ?? $neighbour->created_at)->isoFormat('D MMMM YYYY') }}</time></span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </nav>
                @endif

                <x-newsletter-optin />
            </aside>
        </div>

    </x-page-hero>

    <x-slot:closing>
        <x-closing-cta heading="Zin gekregen om mee te rijden?"
            :href="route('activities.index')" label="Vind een rit" />
    </x-slot:closing>
</x-layouts::site>
