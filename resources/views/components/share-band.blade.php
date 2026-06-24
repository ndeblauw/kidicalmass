@props([
    'url',
    'title',
    'date',
    'heading' => 'Ken je een gezin dat dit leuk zou vinden?',
    'subline' => 'Samen fietsen is leuker. Stuur deze rit door, dan staat de straat zondag nog voller met kinderen.',
    // Share-message + email subject default to the ride wording; the basic activity
    // page (workshop/meeting) passes its own so the copy isn't ride-specific.
    'message' => null,
    'subject' => 'Een leuke fietstocht voor jullie gezin',
])

@php
    $shareMessage = $message ?? "Zin om samen te fietsen? {$title} op {$date}, een vrolijke gezinsrit door autovrije straten. Rij je mee? {$url}";
    $whatsappUrl = 'https://wa.me/?text='.rawurlencode($shareMessage);
    $facebookUrl = 'https://www.facebook.com/sharer/sharer.php?u='.rawurlencode($url);
    $mailtoUrl = 'mailto:?subject='.rawurlencode($subject).'&body='.rawurlencode($shareMessage);
@endphp

<section class="share-band" x-data="{ copied: false }">
    <div class="container mx-auto px-4">
        <div class="share-band__inner">
            <div class="share-band__text">
                <h2 class="share-band__title">{{ $heading }}</h2>
                <p class="share-band__body">{{ $subline }}</p>
            </div>

            <div class="share-band__channels">
                {{-- Copy link --}}
                <button type="button"
                    class="share-band__copy"
                    x-on:click="navigator.clipboard.writeText(@js($url)).then(() => { copied = true; setTimeout(() => copied = false, 2000) })"
                    :aria-label="copied ? 'Link gekopieerd' : 'Kopieer de link naar deze rit'">
                    <flux:icon.link class="share-band__copy-icon" aria-hidden="true" />
                    <span x-show="!copied">Kopieer link</span>
                    <span x-show="copied" x-cloak aria-live="polite">Gekopieerd!</span>
                </button>

                {{-- WhatsApp --}}
                <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener"
                    class="share-band__icon share-band__icon--whatsapp"
                    aria-label="Deel via WhatsApp">
                    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/>
                    </svg>
                </a>

                {{-- Facebook --}}
                <a href="{{ $facebookUrl }}" target="_blank" rel="noopener"
                    class="share-band__icon share-band__icon--facebook"
                    aria-label="Deel op Facebook">
                    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                </a>

                {{-- Email --}}
                <a href="{{ $mailtoUrl }}"
                    class="share-band__icon share-band__icon--email"
                    aria-label="Deel via e-mail">
                    <flux:icon.envelope aria-hidden="true" />
                </a>
            </div>
        </div>
    </div>
</section>
