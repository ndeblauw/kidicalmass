{{--
    Help out / "Meehelpen" (P-13, J2)
    Surface pass 2026-06-02 (re-skin to the ride/show kit, per DESIGN.md):
    - HERO reuses .activity-hero* — solid blue full-bleed, daisy, circular PHOTO of real
      volunteers, sky "Doe mee" badge, -3° white headline.
    - ROLES reuse .activity-promises* — yellow band, white tilted cards, red Flux-icon chips
      (emoji are gone; chips are canonical now).
    - A real-photo JOY band (the live site's signature happy vibe), the group picker as the
      climax on a light-blue band, and a quiet "start a group" coda.
    Structure only; appearance lives in app.css (.ho-* deltas on the reused kit). Orientation
    page: motivates and ROUTES (form on the chapter page, ?intent=volunteer#aanmelden).
    Plan: docs/wiki/design/30-skeleton/help-out.md
--}}
<x-layouts::site :title="__('volunteer.title')" :description="__('meta.help_out')">

    <x-page-hero :eyebrow="__('volunteer.hero.eyebrow')" :title="__('volunteer.hero.title')" illustration="img/illustrations/volunteer-with-wrench.svg">

    {{-- PITCH — the cyclist rides in from the left, breaking out past the viewport
         edge (rear wheel runs off-frame), and sits beside the opening pitch. --}}
    <div class="ho-intro">
        <div class="ho-intro__inner">
            <img class="ho-intro__mascot" src="{{ asset('img/illustrations/cyclist-peace-sign.svg') }}" alt="" aria-hidden="true" loading="lazy">
            <div class="ho-intro__text">
                <x-intro-text>
                    <p>{{ __('volunteer.intro') }}</p>
                </x-intro-text>
            </div>
        </div>
    </div>

    {{-- HOE JE KAN HELPEN — carousel (zelfde aanpak als de teamband op de groep-pagina).
         De illustratie + titel blijven als vaste voorgrond links; de kaarten scrollen
         eronder door en vervagen links in het geel (spiegelt de bleed rechts buiten beeld). --}}
    @php
        $roleStruct = [
            ['icon' => 'shield-check', 'color' => 'red'],
            ['icon' => 'calendar-days', 'color' => 'blue'],
            ['icon' => 'megaphone', 'color' => 'green'],
            ['icon' => 'camera', 'color' => 'orange'],
            ['icon' => 'musical-note', 'color' => 'violet'],
        ];
        $helpRoles = collect(__('volunteer.roles.items'))->map(
            fn (array $role, int $i) => ($roleStruct[$i] ?? []) + $role
        )->all();
    @endphp
    <section class="ho-roles" aria-labelledby="ho-roles-title"
        x-data="{
            start: true,
            end: false,
            page(dir) { const t = $refs.track; const card = t.querySelector('.ho-roles__card'); if (!card) return; const step = card.offsetWidth + parseFloat(getComputedStyle(t).columnGap || 0); t.scrollBy({ left: dir * step, behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth' }); },
            update() {
                const t = $refs.track, fg = $refs.fg;
                if (fg) {
                    const mobile = window.matchMedia('(max-width: 47.99rem)').matches;
                    const edge = fg.getBoundingClientRect().right;
                    t.querySelectorAll('.ho-roles__card').forEach(c => {
                        if (mobile) { c.style.opacity = ''; return; }
                        const r = c.getBoundingClientRect();
                        // share of the card still clear of the foreground (1 = fully clear, 0 = fully under)
                        const clear = (r.right - edge) / r.width;
                        // dissolve fully before the card reaches the biker: opaque until 90% clear, gone by 40%
                        c.style.opacity = Math.max(0, Math.min(1, (clear - 0.4) / 0.5));
                    });
                }
                const max = t.scrollWidth - t.clientWidth;
                const card = t.querySelector('.ho-roles__card');
                const step = card ? card.offsetWidth + parseFloat(getComputedStyle(t).columnGap || 0) : 0;
                this.start = t.scrollLeft <= 1;
                // within half a card of the end, tolerant of the few-px gap mandatory
                // snap leaves between the last snap point and the raw max scroll
                this.end = step > 0 && max - t.scrollLeft <= step / 2;
            }
        }"
        x-init="$nextTick(() => update())"
        x-on:resize.window="update()">

        {{-- foreground anchor: the title holds the left fade zone. Cards scroll behind
             it and fade out (opacity) as they pass under. --}}
        <div class="ho-roles__fg" x-ref="fg">
            <h2 id="ho-roles-title" class="ho-roles__title">{{ __('volunteer.roles.title') }}</h2>
        </div>

        <ul class="ho-roles__track" x-ref="track" role="list" aria-label="{{ __('volunteer.roles.list_label') }}" x-on:scroll.passive="update()">
            @foreach ($helpRoles as $role)
                <li class="ho-roles__card">
                    <x-feature-card :icon="$role['icon']" :color="$role['color']" :title="$role['name']">
                        {{ $role['text'] }}
                    </x-feature-card>
                </li>
            @endforeach
        </ul>

        <div class="ho-roles__nav">
            <button type="button" class="ho-roles__btn" aria-label="{{ __('volunteer.roles.prev') }}" x-on:click="page(-1)" :disabled="start">
                <flux:icon.chevron-left aria-hidden="true" />
            </button>
            <button type="button" class="ho-roles__btn" aria-label="{{ __('volunteer.roles.next') }}" x-on:click="page(1)" :disabled="end">
                <flux:icon.chevron-right aria-hidden="true" />
            </button>
        </div>
    </section>

    {{-- WAT MEEDOEN INHOUDT — scroll-sequence (gedeelde component). De collage rechts
         crossfade't naar het blok dat je leest; een zwaaiende fietser ankert linksonder.
         Mobiel: beide collages gestapeld, geen swap. --}}
    <section class="ho-deal">
        <div class="container mx-auto px-4">
            <x-scroll-sequence media-side="right" active-margin="-25% 0px -66% 0px">
                <x-slot:media>
                    <div class="ho-deal__collage ho-deal__collage--a is-active" data-seq-media="0">
                        <figure class="ho-deal__photo ho-deal__photo--lead">
                            <x-photo src="img/photography/ride-trio-pink-vest-lei-portrait.webp" alt="{{ __('volunteer.photos.roles_1') }}" />
                        </figure>
                        <figure class="ho-deal__photo ho-deal__photo--trail">
                            <x-photo src="img/photography/team-blue-sweatshirts-celebration.webp" alt="{{ __('volunteer.photos.roles_2') }}" />
                        </figure>
                        <img class="ho-deal__doodle" src="{{ asset('img/illustrations/waving-rider.svg') }}" alt="" aria-hidden="true">
                    </div>
                    <div class="ho-deal__collage ho-deal__collage--b" data-seq-media="1">
                        <figure class="ho-deal__photo ho-deal__photo--lead">
                            <x-photo src="img/photography/ride-crowd-intersection.webp" alt="{{ __('volunteer.photos.roles_3') }}" />
                        </figure>
                        <figure class="ho-deal__photo ho-deal__photo--trail">
                            <x-photo src="img/photography/volunteers-season-launch-meetup.webp" alt="{{ __('volunteer.photos.roles_4') }}" />
                        </figure>
                        <img class="ho-deal__doodle" src="{{ asset('img/illustrations/waving-rider.svg') }}" alt="" aria-hidden="true">
                    </div>
                </x-slot:media>

                <div class="scroll-sequence__block" data-seq-block="0">
                    <x-titled-list-block :title="__('volunteer.deal.get.title')" variant="get" level="h2">
                        @foreach (__('volunteer.deal.get.items') as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </x-titled-list-block>
                </div>

                <div class="scroll-sequence__block" data-seq-block="1">
                    <x-titled-list-block :title="__('volunteer.deal.ask.title')" variant="ask" level="h2">
                        @foreach (__('volunteer.deal.ask.items') as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </x-titled-list-block>
                </div>
            </x-scroll-sequence>
        </div>
    </section>

    {{-- VIND JE LOKALE GROEP — light-blue band, the climax. Tap your group → its form. --}}
    <section class="ho-find">
        <div class="container mx-auto px-4">
            <div class="ho-find__layout">
                <div class="ho-find__art" aria-hidden="true">
                    <img src="{{ asset('img/illustrations/zone-30-sign.svg') }}" alt="" loading="lazy">
                </div>

                <div class="ho-find__body">
                    <h2 class="ho-find__title">{{ __('volunteer.find.title') }}</h2>
                    <p class="ho-find__lead">
                        {{ __('volunteer.find.lead') }}
                    </p>

                    <div class="ho-find__picker">
                        <livewire:location-picker :compact="true" />
                    </div>

                    @if ($location && $nearestGroups->isNotEmpty())
                        <h3 class="ho-find__nearest-title">{{ __('volunteer.find.nearest_title', ['name' => $location['name']]) }}</h3>
                        <p class="ho-find__nearest">
                            @foreach ($nearestGroups as $row)
                                <a href="{{ localized_route('groups.show', ['group' => $row['item'], 'intent' => 'volunteer']) }}#aanmelden">{{ $row['item']->name }}</a>@if (! $loop->last), @endif
                            @endforeach
                        </p>
                    @endif

                    <p class="ho-find__all">
                        <a href="{{ localized_route('groups.index') }}">{{ __('volunteer.find.all') }}</a>
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Scroll reveal for the role cards (mirrors the ride page) --}}
    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

        const cards = document.querySelectorAll('.ho-roles .activity-promises__item');
        cards.forEach((card, i) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.4s cubic-bezier(0.25, 1, 0.5, 1), transform 0.4s cubic-bezier(0.25, 1, 0.5, 1)';
            card.style.transitionDelay = `${i * 80}ms`;
        });

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });

        cards.forEach(card => observer.observe(card));
    });
    </script>
    @endpush

    </x-page-hero>

    <x-slot:closing>
        {{-- Closing coda on the yellow band: the "start a group" route replaces the
             generic membership CTA on this page. Content left, sign-holder right. --}}
        <section class="ho-coda relative z-30 bg-kidical-yellow">
            <div class="container mx-auto px-4">
                <div class="ho-coda__layout">
                    <div class="ho-coda__body">
                        <h2 class="ho-coda__title">{{ __('volunteer.coda.title') }}</h2>
                        <p>
                            {{ __('volunteer.coda.body') }}
                        </p>
                        <p class="ho-coda__cta">
                            <x-cta-button :href="localized_route('groups.start')" variant="blue">{{ __('volunteer.coda.cta') }}</x-cta-button>
                        </p>
                    </div>

                    <div class="ho-coda__art" aria-hidden="true">
                        <img src="{{ asset('img/illustrations/heart-sign-holder.svg') }}" alt="" loading="lazy">
                    </div>
                </div>
            </div>
        </section>
    </x-slot:closing>

</x-layouts::site>
