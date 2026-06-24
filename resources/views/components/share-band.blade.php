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
    // Render as a quiet contained panel inside the page container instead of a
    // full-bleed band (e.g. the ride page).
    'contained' => false,
])

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
