@props(['group'])

@php
    // Mirror header.blade.php: the visitor's visible chapters drive the switcher.
    $myChapters = \Illuminate\Support\Facades\Auth::check()
        ? \Illuminate\Support\Facades\Auth::user()->groups()->where('invisible', false)->orderBy('name')->get()
        : collect();

    $place = \Illuminate\Support\Str::of($group->name)->replaceMatches('/^\s*kidical\s+mass\s+/i', '')->trim();
    $hasSwitcher = $myChapters->count() > 1;
@endphp

<div class="roze-shell-bar">
    <div class="roze-shell-bar__inner">
        <a href="{{ route('home') }}" class="roze-shell-bar__logo" aria-label="Terug naar Kidical Mass">
            <img src="{{ asset('img/logos/logo-icon.png') }}" alt="Kidical Mass" class="roze-shell-bar__mark">
        </a>

        @if ($hasSwitcher)
            <flux:dropdown>
                <button type="button" class="roze-shell-switch roze-shell-bar__context" aria-label="Wissel van groep">
                    <span class="roze-shell-bar__place">{{ $place }}</span>
                    <span class="roze-shell-bar__role">roze hesjes</span>
                    <flux:icon name="chevron-down" class="size-4" aria-hidden="true" />
                </button>
                <flux:menu>
                    @foreach ($myChapters as $chapter)
                        <flux:menu.item href="{{ route('groups.roze-hesjes', $chapter) }}">
                            {{ \Illuminate\Support\Str::of($chapter->name)->replaceMatches('/^\s*kidical\s+mass\s+/i', '')->trim() }}
                        </flux:menu.item>
                    @endforeach
                </flux:menu>
            </flux:dropdown>
        @else
            <span class="roze-shell-bar__context">
                <span class="roze-shell-bar__place">{{ $place }}</span>
                <span class="roze-shell-bar__role">roze hesjes</span>
            </span>
        @endif

        @auth
            <div class="roze-shell-bar__account">
                <x-account-menu />
            </div>
        @endauth
    </div>
</div>
