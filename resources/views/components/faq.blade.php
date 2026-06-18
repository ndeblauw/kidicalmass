{{-- Accordion of question/answer pairs. Native <details> with an animated
     open/close (no JS). Compose with <x-faq.item question="…">answer…</x-faq.item>.
     Appearance lives in resources/css/components/faq.css. --}}
<div {{ $attributes->merge(['class' => 'faq']) }}>
    {{ $slot }}
</div>
