{{-- Check list: a green check-chip bullet list (the same chip language as
     titled-list-block--get, without the title). Use for benefit/offer lists;
     items are plain <li> in the slot. Styling: components/check-list.css. --}}
<ul {{ $attributes->merge(['class' => 'check-list']) }} role="list">
    {{ $slot }}
</ul>
