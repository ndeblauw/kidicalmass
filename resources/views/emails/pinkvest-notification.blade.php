<x-emails.notification
    color="{{ $colour ?? 'pink' }}"
    subject="{{ $subject ?? '' }}"
    :cta-url="$actionUrl ?? null"
    :cta-label="$actionText ?? null"
>
    @if ($greeting)
    <p style="margin:0 0 6px; color:rgba(0,0,0,0.5); font-size:13px; font-weight:700; letter-spacing:1.5px; text-transform:uppercase;">Kidical Mass</p>
    <h1 style="margin:0 0 28px; color:#281a39; font-size:30px; line-height:1.15; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif; font-weight:800;">{{ $greeting }}</h1>
    @endif

    @foreach ($introLines as $line)
    <p style="margin:0 0 18px; font-size:18px; line-height:1.6;">{!! $line !!}</p>
    @endforeach

    @if ($salutation)
    <p style="margin:18px 0 6px; font-size:18px; line-height:1.6;">{{ $salutation }}</p>
    @endif

    @foreach ($outroLines as $line)
    <p style="margin:0 0 18px; font-size:18px; line-height:1.6;">{!! $line !!}</p>
    @endforeach
</x-emails.notification>
