{{-- One-line GDPR notice under a form's submit button: the purpose sentence
     comes in via the slot, the privacy-page link is appended. Explicit locale
     param so the view also renders outside a routed request (Livewire tests). --}}
<p {{ $attributes->merge(['class' => 'text-sm text-kidical-ink/60']) }}>
    {{ $slot }}
    <a href="{{ route('privacy', ['locale' => app()->getLocale()]) }}" class="underline">Meer weten? Lees onze privacyverklaring.</a>
</p>
