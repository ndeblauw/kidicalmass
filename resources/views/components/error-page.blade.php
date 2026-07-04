@props(['code', 'title', 'illustration'])

{{-- Shared shell for the error pages that keep the site layout (404/403/419):
     illustration, status code, headline, body copy (slot) and an optional
     actions slot. data-error-page is the stable test seam. The standalone
     500/503 views deliberately do NOT use this component: they must render
     without the app fully booting. --}}
<section data-error-page="{{ $code }}" class="mx-auto flex max-w-2xl flex-col items-center gap-4 pt-8 pb-4 text-center">
    <img src="{{ asset($illustration) }}" alt="" aria-hidden="true" class="h-44 w-auto sm:h-56">
    <p class="font-heading text-kidical-blue" aria-hidden="true">{{ $code }}</p>
    <h1>{{ $title }}</h1>
    <div>{{ $slot }}</div>
    @isset($actions)
        <div class="mt-2 flex flex-wrap items-center justify-center gap-4">{{ $actions }}</div>
    @endisset
</section>
