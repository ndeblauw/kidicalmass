@props([
    'url',
    'title',
    'date',
    'heading' => null,
    'subline' => null,
    // Share-message + email subject default to the ride wording; the basic activity
    // page (workshop/meeting) passes its own so the copy isn't ride-specific.
    'message' => null,
    'subject' => null,
    // Render as a quiet contained panel inside the page container instead of a
    // full-bleed band (e.g. the ride page).
    'contained' => false,
])

@php
    $heading = $heading ?? __('components.share.heading');
    $subline = $subline ?? __('components.share.subline');
    $subject = $subject ?? __('components.share.subject');
@endphp

<section @class(['share-band', 'share-band--contained' => $contained])>
    <div @class(['container mx-auto px-4' => ! $contained])>
        <div class="share-band__inner">
            <div class="share-band__text">
                <h2 class="share-band__title">{{ $heading }}</h2>
                <p class="share-band__body">{{ $subline }}</p>
            </div>

            <x-share-links :url="$url" :title="$title" :date="$date" :message="$message" :subject="$subject" />
        </div>
    </div>
</section>
