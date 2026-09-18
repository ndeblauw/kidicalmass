<x-layouts::site :title="__('newsletter.confirmed.title')" :description="__('meta.newsletter')">
    <section class="flex flex-col items-center gap-6 text-center max-w-2xl mx-auto py-16">
        <x-envelope-chips />

        <h1>{{ __('newsletter.confirmed.heading') }}</h1>

        <p class="max-w-xl">
            {{ __('newsletter.confirmed.body') }}
        </p>

        <div class="flex flex-wrap items-center justify-center gap-4">
            <x-cta-button :href="localized_route('activities.index')" variant="yellow" icon="arrow">{{ __('newsletter.confirmed.calendar') }}</x-cta-button>
            <x-cta-button :href="localized_route('groups.index')" variant="secondary" icon="arrow">{{ __('newsletter.confirmed.group') }}</x-cta-button>
        </div>
    </section>
</x-layouts::site>
