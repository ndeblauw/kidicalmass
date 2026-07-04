@props(['group'])

@php
    $classes = 'inline-block rounded-full px-2.5 py-0.5 text-xs font-bold text-kidical-ink/80 '
        .($group->isNationalRoot() ? 'bg-kidical-light-yellow' : 'bg-kidical-light-blue');
@endphp

{{-- Afzender-chip op witte grond: de groep (of "Heel België" voor de
     nationale root) bij nieuwskaarten. Alleen zichtbare groepen linken naar
     hun chapterpagina; regio-/landnodes zijn kale tekst. --}}
@if ($group->hasPublicPage())
    <a href="{{ route('groups.show', $group) }}" {{ $attributes->merge(['class' => 'link-plain transition-colors hover:bg-kidical-sky/60 '.$classes]) }}>{{ $group->publicLabel() }}</a>
@else
    <span {{ $attributes->merge(['class' => $classes]) }}>{{ $group->publicLabel() }}</span>
@endif
