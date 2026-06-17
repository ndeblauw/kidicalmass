@props([
    'preheader' => null,
    'subject'   => 'Kidical Mass',
    'color'     => null,   // null|'blue' → pastel blue (default) | 'yellow' | 'pink'
    'ctaUrl'    => null,
    'ctaLabel'  => null,
])

@php
    $theme = match ($color) {
        'yellow' => ['bg' => '#FEF3D5', 'btn' => '#f9d924', 'btnText' => '#281a39'],
        'pink'   => ['bg' => '#fce4ec', 'btn' => '#E63A7B', 'btnText' => '#ffffff'],
        default  => ['bg' => '#B7E7F0', 'btn' => '#1d67cd', 'btnText' => '#ffffff'],
    };
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
</head>
<body style="margin:0; padding:0; background-color:{{ $theme['bg'] }}; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif; color:#281a39;">

    @if ($preheader)
    <div style="display:none; max-height:0; overflow:hidden; opacity:0;">{{ $preheader }}</div>
    @endif

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:{{ $theme['bg'] }}; padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px; width:100%; background-color:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 6px 30px rgba(0,0,0,0.08);">

                    {{-- Logo --}}
                    <tr>
                        <td style="padding:36px 40px 0; text-align:center;">
                            <img src="{{ asset('img/logos/logo-color.png') }}" alt="Kidical Mass" style="width:200px; height:auto; border:0; display:block; margin:0 auto;">
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding:36px 40px;">
                            {{ $slot }}

                            @if ($ctaUrl && $ctaLabel)
                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin:28px 0 0;">
                                <tr>
                                    <td style="background-color:{{ $theme['btn'] }}; border-radius:9999px;">
                                        <a href="{{ $ctaUrl }}" style="display:inline-block; padding:15px 32px; font-size:17px; font-weight:700; color:{{ $theme['btnText'] }}; text-decoration:none;">{{ $ctaLabel }} →</a>
                                    </td>
                                </tr>
                            </table>
                            @endif
                        </td>
                    </tr>

                    @if ($ctaUrl)
                    {{-- Footer with fallback link --}}
                    <tr>
                        <td style="padding:24px 40px; background-color:#f7f7f5; border-top:1px solid #ececec;">
                            <p style="margin:0; font-size:13px; line-height:1.6; color:#8a8594;">
                                Werkt de knop niet? Plak deze link in je browser:<br>
                                <a href="{{ $ctaUrl }}" style="color:#1d67cd; word-break:break-all;">{{ $ctaUrl }}</a>
                            </p>
                        </td>
                    </tr>
                    @endif

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
