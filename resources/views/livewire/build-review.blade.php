<div class="flex h-screen" data-review-page="{{ $page['id'] }}" @if ($previewUrl) data-preview-url="{{ $previewUrl }}" @endif
     x-data="{ mobile: false }">

    {{-- Preview pane --}}
    <main class="flex-1 flex items-stretch justify-center bg-neutral-200 overflow-hidden">
        @if ($rowMissing)
            <p class="self-center text-neutral-500" data-review-missing-row>Rij {{ $pageId }} staat niet meer in het register. Herlaad de pagina of controleer de registry-markdown.</p>
        @elseif ($previewUrl)
            <div class="h-full transition-all" :class="mobile ? 'w-[390px]' : 'w-full'">
                <iframe src="{{ $previewUrl }}" title="{{ $page['name'] }}" class="w-full h-full border-0 bg-white"></iframe>
            </div>
        @else
            <p class="self-center text-neutral-500" data-preview-missing>Geen live preview voor deze rij.</p>
        @endif
    </main>

    {{-- Sidebar --}}
    <aside class="w-80 shrink-0 flex flex-col gap-4 p-4 bg-white border-l border-neutral-200 overflow-y-auto">
        <header class="flex flex-col gap-1">
            <a href="{{ route('build.dashboard') }}" class="text-xs text-neutral-500">← dashboard</a>
            <h1 class="font-bold">{{ $page['id'] }} · {{ $page['name'] }}</h1>
            <div class="flex items-center justify-between text-sm">
                <span class="text-neutral-500">{{ $index + 1 }}/{{ $total }} · <code>{{ $page['slug'] }}</code></span>
                <span class="flex gap-2">
                    @if ($prev)<a href="{{ route('build.review', $prev) }}">←</a>@endif
                    @if ($next)<a href="{{ route('build.review', $next) }}">→</a>@endif
                </span>
            </div>
            @if ($previewUrl)
                <div class="flex gap-2 text-xs">
                    <button type="button" @click="mobile = false" :class="! mobile && 'font-bold'">desktop</button>
                    <button type="button" @click="mobile = true" :class="mobile && 'font-bold'">mobiel 390px</button>
                    <a href="{{ $previewUrl }}" target="_blank" class="ml-auto">open in tab ↗</a>
                </div>
            @endif
        </header>

        @unless ($rowMissing)
            <section class="grid grid-cols-2 gap-2">
                @foreach (['ux' => 'UX', 'wireframe' => 'Wire', 'assets' => 'Assets', 'ui' => 'UI', 'back' => 'Back', 'cms' => 'CMS', 'ok' => 'OK'] as $key => $label)
                    <button type="button" wire:click="cycle('{{ $key }}')" data-stage="{{ $key }}"
                            class="flex items-center justify-between rounded border border-neutral-200 px-3 py-2 text-sm hover:bg-neutral-50">
                        <span>{{ $label }}</span><span>{{ $stages[$key] }}</span>
                    </button>
                @endforeach
                <label class="col-span-2 flex items-center justify-between rounded border border-neutral-200 px-3 py-2 text-sm">
                    Conf (1–5)
                    <input type="number" min="1" max="5" wire:model="confidence" data-stage="conf" class="w-14 text-right">
                </label>
            </section>

            <section class="flex flex-col gap-2">
                <textarea wire:model="feedback" rows="6" data-review-feedback
                          class="rounded border border-neutral-200 p-2 text-sm"
                          placeholder="Feedback voor deze pagina (komt in review-inbox.md)"></textarea>
                @error('save')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                <div class="flex gap-2">
                    <button type="button" wire:click="save(false)" class="rounded border border-neutral-300 px-3 py-2 text-sm">Bewaar</button>
                    <button type="button" wire:click="save(true)" class="flex-1 rounded bg-neutral-900 px-3 py-2 text-sm text-white">Bewaar en volgende</button>
                </div>
            </section>
        @endunless

        @if ($inboxPending)
            <p class="mt-auto rounded bg-amber-50 p-2 text-xs text-amber-800" data-reconcile-hint>
                Er staat feedback in <code>review-inbox.md</code>. Top gaps en Roll-up lopen achter: verwerk na de sessie via <code>/pipeline</code>.
            </p>
        @endif
    </aside>
</div>
