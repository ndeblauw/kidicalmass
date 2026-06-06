<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welkom bij Kidical Mass {{ $group->name }}</title>
</head>
<body style="margin:0; padding:0; background-color:#FEF3D5; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif; color:#281a39;">

    {{-- Preheader (hidden) --}}
    <div style="display:none; max-height:0; overflow:hidden; opacity:0;">Activeer je account en maak je klaar voor je eerste rit.</div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#FEF3D5; padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px; width:100%; background-color:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 6px 30px rgba(0,0,0,0.08);">

                    {{-- Header band --}}
                    <tr>
                        <td style="background-color:#1d67cd; padding:36px 40px;">
                            <p style="margin:0 0 6px; color:rgba(255,255,255,0.75); font-size:13px; font-weight:700; letter-spacing:1.5px; text-transform:uppercase;">Kidical Mass {{ $group->name }}</p>
                            <h1 style="margin:0; color:#ffffff; font-size:30px; line-height:1.15;">Welkom bij de roze hesjes! 🦺</h1>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding:36px 40px;">
                            <p style="margin:0 0 18px; font-size:18px; line-height:1.6;">Dag {{ $firstName }},</p>

                            <p style="margin:0 0 18px; font-size:18px; line-height:1.6;">
                                Leuk dat je meefietst met Kidical Mass {{ $group->name }}. Je hoort er nu
                                officieel bij als roze hesje.
                            </p>

                            <p style="margin:0 0 28px; font-size:18px; line-height:1.6;">
                                We hebben een plek voor je gemaakt met alles wat je nodig hebt: hoe een rit
                                verloopt, wat je rol is, en wie er in je team zit. Voortaan op één plek terug
                                te vinden, op je gsm én je laptop.
                            </p>

                            {{-- CTA --}}
                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 0 28px;">
                                <tr>
                                    <td style="background-color:#f9d924; border-radius:9999px;">
                                        <a href="{{ $activateUrl }}" style="display:inline-block; padding:15px 32px; font-size:17px; font-weight:700; color:#281a39; text-decoration:none;">Activeer je account →</a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 6px; font-size:18px; line-height:1.6;">Tot op de volgende rit!</p>
                            <p style="margin:0; font-size:18px; line-height:1.6; font-weight:700;">Het team van Kidical Mass {{ $group->name }}</p>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding:24px 40px; background-color:#f7f7f5; border-top:1px solid #ececec;">
                            <p style="margin:0; font-size:13px; line-height:1.6; color:#8a8594;">
                                Werkt de knop niet? Plak deze link in je browser:<br>
                                <a href="{{ $activateUrl }}" style="color:#1d67cd; word-break:break-all;">{{ $activateUrl }}</a>
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
