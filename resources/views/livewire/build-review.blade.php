<div data-review-page="{{ $page['id'] }}" @if ($previewUrl) data-preview-url="{{ $previewUrl }}" @endif>
    <header>
        <a href="{{ route('build.dashboard') }}">← dashboard</a>
        <h1>{{ $page['id'] }} · {{ $page['name'] }} <small>({{ $index + 1 }}/{{ $total }})</small></h1>
        @if ($prev) <a href="{{ route('build.review', $prev) }}">← vorige</a> @endif
        @if ($next) <a href="{{ route('build.review', $next) }}">volgende →</a> @endif
    </header>

    @if ($previewUrl)
        <iframe src="{{ $previewUrl }}" title="{{ $page['name'] }}"></iframe>
    @else
        <p data-preview-missing>Geen live preview voor deze rij.</p>
    @endif

    <section>
        @foreach (['ux' => 'UX', 'wireframe' => 'Wire', 'assets' => 'Assets', 'ui' => 'UI', 'back' => 'Back', 'ok' => 'OK'] as $key => $label)
            <button type="button" wire:click="cycle('{{ $key }}')" data-stage="{{ $key }}">
                {{ $label }} {{ $stages[$key] }}
            </button>
        @endforeach
        <label>Conf <input type="number" min="1" max="5" wire:model="confidence" data-stage="conf"></label>
        <textarea wire:model="feedback" placeholder="Feedback voor deze pagina" data-review-feedback></textarea>
        <button type="button" wire:click="save(false)">Bewaar</button>
        <button type="button" wire:click="save(true)">Bewaar en volgende</button>
    </section>
</div>
