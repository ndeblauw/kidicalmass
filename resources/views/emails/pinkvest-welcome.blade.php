<x-emails.notification
    color="pink"
    subject="Welkom bij de roze hesjes van {{ $group->name }}"
    preheader="Je hoort er nu officieel bij — welkom bij het team!"
>
    <p style="margin:0 0 6px; color:rgba(0,0,0,0.5); font-size:13px; font-weight:700; letter-spacing:1.5px; text-transform:uppercase;">Kidical Mass {{ $group->name }}</p>
    <h1 style="margin:0 0 28px; color:#281a39; font-size:30px; line-height:1.15; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif; font-weight:800;">Welkom bij de roze hesjes! 🦺</h1>

    <p style="margin:0 0 18px; font-size:18px; line-height:1.6;">Dag {{ $firstName }},</p>

    <p style="margin:0 0 18px; font-size:18px; line-height:1.6;">
        Leuk dat je meefietst met Kidical Mass {{ $group->name }}. Je hoort er nu
        officieel bij als roze hesje.
    </p>

    <p style="margin:0 0 18px; font-size:18px; line-height:1.6;">
        We hebben een plek voor je gemaakt met alles wat je nodig hebt: hoe een rit
        verloopt, wat je rol is, en wie er in je team zit. Voortaan op één plek terug
        te vinden, op je gsm én je laptop.
    </p>

    <p style="margin:18px 0 6px; font-size:18px; line-height:1.6;">Tot op de volgende rit!</p>
    <p style="margin:0; font-size:18px; line-height:1.6; font-weight:700;">Het team van Kidical Mass {{ $group->name }}</p>
</x-emails.notification>
