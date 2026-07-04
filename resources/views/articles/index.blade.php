{{--
    Over ons / Nieuws — /about/news (P-18)
    News pass 2026-07: about-frame hero, featured-first feed (the newest
    article renders as a wide x-article-feature, the rest in the
    x-article-card grid; page 2+ is just the grid). Empty state via
    <x-empty-state>. Copy: lang/nl/about.php (news_*). Structure only.
--}}
<x-layouts::site :title="__('nav.news')" :description="__('meta.news')">

    <x-page-hero
        :eyebrow="__('nav.news')"
        :title="__('about.news_title')"
        size="compact">

    <div class="space-y-10">
        <div class="max-w-prose">
            <x-intro-text>
                <p>{{ __('about.news_lead') }}</p>
            </x-intro-text>
        </div>

        @if ($articles->isNotEmpty())
            @if ($feature)
                <x-article-feature :article="$feature" />
            @endif

            @if ($gridArticles->isNotEmpty())
                <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3" data-article-grid>
                    @foreach ($gridArticles as $article)
                        <x-article-card :article="$article" />
                    @endforeach
                </div>
            @endif

            <div>{{ $articles->links() }}</div>
        @else
            <x-empty-state :heading="__('about.news_empty_title')">
                {!! __('about.news_empty_body', [
                    'instagram' => '<a href="'.config('kidicalmass.social.instagram').'" target="_blank" rel="noopener noreferrer">Instagram</a>',
                    'facebook' => '<a href="'.config('kidicalmass.social.facebook').'" target="_blank" rel="noopener noreferrer">Facebook</a>',
                ]) !!}
            </x-empty-state>
        @endif
    </div>

    @push('scripts')
    <x-scroll-reveal selector="[data-article-grid] > article" :transform="true" />
    @endpush

    </x-page-hero>

    <x-slot:closing>
        <x-closing-cta heading="Zin gekregen om mee te rijden?"
            :href="route('activities.index')" label="Vind een rit" />
    </x-slot:closing>
</x-layouts::site>
