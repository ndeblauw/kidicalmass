<x-roze-hub :group="$group" active="aan-de-slag" :is-captain="$isCaptain" :show-welcome="$showWelcome" :beheer-url="$beheerUrl" :own-heading="true">
    @php
        $gemeente = trim((string) preg_replace('/^\s*kidical\s+mass\s+/i', '', $group->name));
        $gemeente = $gemeente !== '' ? $gemeente : $group->name;
    @endphp

    {{-- 5 · VOOR JE EERSTE RIT — permanent onboarding (always here, so the welcome block's
         info stays findable after it expires). The startspeech is NOT here — it is kapitein
         material (a besloten tile in Jouw materiaal). --}}
    <section id="voor-je-eerste-rit" class="roze-onboarding scroll-mt-24">
        <h1 class="roze-hub-title">{{ __('roze.start.title') }}</h1>
        <p class="roze-hub-lead">{{ __('roze.start.lead') }}</p>

        <h2 class="roze-onboarding__sub">{{ __('roze.start.what_title') }}</h2>
        <div class="roze-onboarding__cards">
            <x-roze-card heading="h3" icon="users" :title="__('roze.start.cards.ride.title')" color="red">
                {{ __('roze.start.cards.ride.body') }}
            </x-roze-card>
            <x-roze-card heading="h3" icon="sparkles" :title="__('roze.start.cards.presence.title')" color="orange">
                {{ __('roze.start.cards.presence.body') }}
            </x-roze-card>
            <x-roze-card heading="h3" icon="eye" :title="__('roze.start.cards.visibility.title')" color="blue">
                {{ __('roze.start.cards.visibility.body') }}
            </x-roze-card>
            <x-roze-card heading="h3" icon="academic-cap" :title="__('roze.start.cards.training.title')" color="green">
                {{ __('roze.start.cards.training.body') }}
            </x-roze-card>
        </div>

        {{-- Begeleidingsvideo — embedded inline (privacy-friendly nocookie host), with a
             localized caption so the page frames it (the YouTube overlay is English). --}}
        <figure class="roze-video-figure">
            <div class="roze-video">
                <iframe
                    src="https://www.youtube-nocookie.com/embed/i9YQxJ-ChNM"
                    title="{{ __('roze.start.video_title') }}"
                    loading="lazy"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    allowfullscreen
                ></iframe>
            </div>
            <figcaption class="roze-video__caption">{{ __('roze.start.video_caption') }}</figcaption>
        </figure>

        {{-- WHATSAPP-DOORGANG — the community hand-off, lifted up so it's found early; the
             green WhatsApp glyph makes it instantly scannable. FAUX href until Nico #37. --}}
        <section class="roze-whatsapp">
            <div class="roze-whatsapp__inner">
                <div class="roze-whatsapp__lede">
                    <x-icon-chip color="green" size="md" :shadow="true">
                        <svg viewBox="0 0 24 24" fill="currentColor" class="size-6" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/></svg>
                    </x-icon-chip>
                    <div>
                        <h2 class="roze-whatsapp__title roze-card-title">{{ __('roze.start.whatsapp_title', ['place' => $gemeente]) }}</h2>
                        <p class="roze-whatsapp__body">{{ __('roze.start.whatsapp_body') }}</p>
                    </div>
                </div>
                {{-- No live URL yet (Nico #37): an honest "binnenkort" control, not a button to nowhere. --}}
                <span class="roze-whatsapp__btn roze-whatsapp__btn--soon">{{ __('roze.start.whatsapp_cta') }} <small>({{ __('roze.start.whatsapp_soon') }})</small></span>
            </div>
        </section>

        <h2 class="roze-onboarding__sub">{{ __('roze.start.steps_title') }}</h2>
        <ol role="list" class="roze-steps">
            @foreach ([
                ['title' => __('roze.start.steps.before.title'), 'body' => __('roze.start.steps.before.body')],
                ['title' => __('roze.start.steps.during.title'), 'body' => __('roze.start.steps.during.body')],
                ['title' => __('roze.start.steps.pace.title'), 'body' => __('roze.start.steps.pace.body')],
                ['title' => __('roze.start.steps.crossings.title'), 'body' => __('roze.start.steps.crossings.body')],
            ] as $i => $step)
                <li class="roze-step">
                    <span class="roze-step__num">{{ $i + 1 }}</span>
                    <div>
                        <strong class="roze-step__title roze-card-title">{{ $step['title'] }}</strong>
                        <p class="roze-step__body">{{ $step['body'] }}</p>
                    </div>
                </li>
            @endforeach
        </ol>
    </section>
</x-roze-hub>
