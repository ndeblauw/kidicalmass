@props(['variant' => 'home', 'title' => null, 'body' => null])

{{-- Contextual "Steun" block (PAT-10). Copy comes from the `variant` (home / event),
     or pass explicit `title`/`body` for a page-specific ask (e.g. the About leaves).
     Every variant carries the riding-stays-free clause. Links to route('membership'). --}}
<section class="support-callout support-callout--{{ $variant }}">
    <div class="container mx-auto px-4">
        <div class="support-callout__inner">
            <div class="support-callout__text">
                <h2 class="support-callout__title">{{ $title ?? __('support.'.$variant.'_title') }}</h2>
                <p class="support-callout__body">{{ $body ?? __('support.'.$variant.'_body') }}</p>
            </div>
            <x-cta-button :href="route('membership')" icon="heart" class="shrink-0">{{ __('support.cta') }}</x-cta-button>
        </div>
    </div>
</section>
