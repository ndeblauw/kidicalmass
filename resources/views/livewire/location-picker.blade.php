{{-- One line in every state: the empty prompt and the chosen place share the same
     sentence shape, so picking a place fills the gap instead of reflowing the page. --}}
<div
    class="location-picker"
    x-data="{
        init() {
            // Picking a location triggers a navigate to the same page; restore the scroll
            // position we stashed just before, so the user stays put instead of jumping to the top.
            // After a clear, the picker may render in another slot (the homepage moves it below
            // the list), so bring it back into view if the restored position no longer shows it.
            const y = sessionStorage.getItem('lp-scroll');
            const keepInView = sessionStorage.getItem('lp-keep-in-view') !== null;
            sessionStorage.removeItem('lp-keep-in-view');
            if (y !== null) {
                sessionStorage.removeItem('lp-scroll');
                requestAnimationFrame(() => requestAnimationFrame(() => {
                    window.scrollTo(0, parseInt(y, 10));
                    if (! keepInView) { return; }
                    const box = this.$root.getBoundingClientRect();
                    if (box.top < 0 || box.bottom > window.innerHeight) {
                        this.$root.scrollIntoView({ block: 'center' });
                    }
                }));
            }
        },
        stashScroll(keepInView = false) {
            sessionStorage.setItem('lp-scroll', window.scrollY);
            if (keepInView) { sessionStorage.setItem('lp-keep-in-view', '1'); }
        },
        options() { return [...this.$root.querySelectorAll('[data-option]')]; },
        focusFirst() { this.options()[0]?.focus(); },
        move(dir, current) {
            const opts = this.options();
            if (! opts.length) { return; }
            const i = opts.indexOf(current);
            (dir > 0 ? (opts[i + 1] ?? this.$refs.input) : (i <= 0 ? this.$refs.input : opts[i - 1])).focus();
        },
        dismiss() { this.$refs.input.focus(); this.$wire.set('query', ''); },
        escape() {
            if (this.$wire.query !== '') { this.$wire.set('query', ''); return; }
            if (this.$wire.editing) { this.$wire.cancelEditing(); }
        }
    }"
    @focus-picker.window="$wire.set('editing', true); $nextTick(() => $el.querySelector('input')?.focus())"
>
    <div class="location-picker__line">
        <span class="location-picker__pin" aria-hidden="true">
            <svg viewBox="0 0 40 54" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M20 2C10.059 2 2 10.059 2 20C2 32 20 52 20 52C20 52 38 32 38 20C38 10.059 29.941 2 20 2Z" fill="var(--color-kidical-red)"/>
                <circle cx="20" cy="20" r="7.5" fill="rgba(0,0,0,0.2)"/>
                <circle cx="20" cy="20" r="4.5" fill="white"/>
            </svg>
        </span>

        @if ($current && ! $editing)
            <p class="location-picker__current" data-state="chosen">
                {{ __('common.location.current') }} <strong class="location-picker__name">{{ $current['name'] }}</strong>
            </p>
            <button type="button" wire:click="$set('editing', true)" class="location-picker__action link-plain">{{ __('common.location.change') }}</button>
            <button type="button" wire:click="clear" @unless ($reactive) x-on:click="stashScroll(true)" @endunless class="location-picker__action link-plain" data-location-clear aria-label="{{ __('common.location.clear_label') }}">{{ __('common.location.clear') }}</button>
        @else
            <label class="location-picker__label" for="location-picker-query" data-state="{{ $current ? 'editing' : 'empty' }}">
                {{ $current ? __('common.location.current') : __('common.location.prompt') }}
                @if ($current)
                    <span class="sr-only">{{ __('common.location.placeholder') }}</span>
                @endif
            </label>
            <input
                id="location-picker-query"
                x-ref="input"
                type="text"
                role="combobox"
                aria-autocomplete="list"
                aria-controls="location-picker-suggestions"
                aria-expanded="{{ $suggestions->isNotEmpty() ? 'true' : 'false' }}"
                wire:model.live.debounce.250ms="query"
                placeholder="{{ $current ? $current['name'] : __('common.location.placeholder') }}"
                autocomplete="off"
                class="location-picker__input"
                @if ($editing) x-init="$nextTick(() => $el.focus())" @endif
                x-on:keydown.down.prevent="focusFirst()"
                x-on:keydown.escape.prevent="escape()"
            >
            @if ($current)
                <button type="button" wire:click="cancelEditing" class="location-picker__action link-plain">{{ __('common.location.cancel') }}</button>
            @endif
        @endif
    </div>

    {{-- Always-rendered live region: announces how many suggestions the
         typed query produced, since the listbox itself appears silently. --}}
    <p class="sr-only" role="status">
        @if ($suggestions->isNotEmpty())
            {{ trans_choice('common.location.suggestions_status', $suggestions->count(), ['count' => $suggestions->count()]) }}
        @endif
    </p>

    @if ($suggestions->isNotEmpty())
        <ul id="location-picker-suggestions" role="listbox" aria-label="{{ __('common.location.suggestions_label') }}" class="location-picker__suggestions">
            @foreach ($suggestions as $pc)
                <li role="presentation" wire:key="pc-{{ $pc->zip }}">
                    <button
                        type="button"
                        role="option"
                        aria-selected="false"
                        data-option
                        wire:click="choose('{{ $pc->zip }}')"
                        class="location-picker__suggestion link-plain"
                        x-on:click="stashScroll()"
                        x-on:focus="$el.setAttribute('aria-selected', true)"
                        x-on:blur="$el.setAttribute('aria-selected', false)"
                        x-on:keydown.down.prevent="move(1, $el)"
                        x-on:keydown.up.prevent="move(-1, $el)"
                        x-on:keydown.escape.prevent="dismiss()"
                    >
                        <strong>{{ $pc->zip }}</strong> <span>{{ $pc->name }}</span>
                    </button>
                </li>
            @endforeach
        </ul>
    @endif
</div>
