@props([
    'question',
])

<details class="faq__item">
    <summary class="faq__q">{{ $question }}</summary>
    <div class="faq__a">{{ $slot }}</div>
</details>
