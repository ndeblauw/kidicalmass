@props(['code', 'title', 'illustration'])

{{-- Shared shell for the error pages that keep the site layout (404/403/419):
     illustration, status code, headline, body copy (slot) and an optional
     actions slot. data-error-page is the stable test seam. The standalone
     500/503 views deliberately do NOT use this component: they must render
     without the app fully booting. --}}
@push('scripts')
    {{-- Livewire's auto-injector skips non-200 responses (SupportAutoInjectedAssets
         checks status === 200), so Alpine (bundled in livewire.js) never loads on
         error pages and the mobile nav renders stuck open. Render the scripts
         explicitly; error statuses are always non-200, so this cannot double-inject. --}}
    @livewireScripts
@endpush

{{-- Mobile: centered stack, illustration on top. From sm: two columns — copy left,
     tall sign right — echoing the page-hero's copy/artwork split and halving the
     hero's height. --}}
<section data-error-page="{{ $code }}" class="mx-auto grid max-w-4xl items-center justify-items-center gap-x-16 gap-y-6 pt-8 pb-4 text-center sm:grid-cols-[minmax(0,1fr)_auto] sm:justify-items-start sm:text-left">
    <img src="{{ asset($illustration) }}" alt="" aria-hidden="true" class="h-40 w-auto sm:order-2 sm:h-72 sm:justify-self-end">
    <div class="flex flex-col items-center gap-4 sm:order-1 sm:items-start">
        <p class="font-heading text-kidical-blue" aria-hidden="true">{{ $code }}</p>
        <h1>{{ $title }}</h1>
        <div>{{ $slot }}</div>
        @isset($actions)
            <div class="mt-2 flex flex-wrap items-center justify-center gap-4 sm:justify-start">{{ $actions }}</div>
        @endisset
    </div>
</section>
