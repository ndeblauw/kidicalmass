@props(['title', 'sections' => []])

<x-layouts::site :title="$title">
    <div class="mx-auto max-w-3xl space-y-8">
        <header class="space-y-2">
            <h1>{{ $title }}</h1>
            <p class="inline-block rounded bg-yellow-100 px-3 py-1 text-sm font-bold text-kidical-ink">
                Stub — alleen structuur, nog geen definitieve inhoud.
            </p>
        </header>

        @foreach ($sections as $heading => $note)
            <section class="space-y-2">
                <h2>{{ $heading }}</h2>
                <p class="text-kidical-ink/50">[ placeholder: {{ $note }} ]</p>
            </section>
        @endforeach
    </div>
</x-layouts::site>
