{{--
    Extra-info note: the organisers' free text for one activity ("Iets speciaals deze
    keer?"), shown as a calm note (role="note", not an aside landmark) with a small eyebrow label naming who wrote it.

    Props:
    - heading: eyebrow label, e.g. "Van Kidical Mass Gent".
    - body: sanitised HTML (Illuminate\Support\HtmlString) from
      App\Support\RideText::renderExtraInfo. Printed with {{ }}; never {!! !!}.
    - id: base for the label id that names the note (aria-labelledby).

    Styling lives in resources/css/components/extra-info.css (the body arrives
    class-less from markdown, so it needs descendant rules).
--}}
@props(['heading', 'body', 'id' => 'activity-extra-info'])

<div {{ $attributes->merge(['class' => 'extra-info']) }} role="note" data-activity-extra-info aria-labelledby="{{ $id }}-label">
    <p id="{{ $id }}-label" class="extra-info__label">{{ $heading }}</p>
    <div class="extra-info__body">{{ $body }}</div>
</div>
