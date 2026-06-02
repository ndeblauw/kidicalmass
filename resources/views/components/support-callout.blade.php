@props(['variant' => 'home'])

{{-- Contextual "Steun" block (PAT-10). Two copy variants (home / event); every
     variant carries the riding-stays-free clause. Links to route('membership'). --}}
<section class="support-callout support-callout--{{ $variant }}">
    <div class="container mx-auto px-4">
        <div class="support-callout__inner">
            <div class="support-callout__text">
                <h2 class="support-callout__title">{{ __('support.'.$variant.'_title') }}</h2>
                <p class="support-callout__body">{{ __('support.'.$variant.'_body') }}</p>
            </div>
            <a href="{{ route('membership') }}" class="support-callout__cta">
                <flux:icon.heart variant="solid" class="support-callout__cta-icon" aria-hidden="true" />
                {{ __('support.cta') }}
            </a>
        </div>
    </div>
</section>
