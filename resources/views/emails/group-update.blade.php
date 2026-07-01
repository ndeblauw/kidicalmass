@php
    use App\Support\RideDate;
    use Illuminate\Support\Str;

    /** @var \Illuminate\Support\Collection<\App\Actions\GroupChangesResult> $changes */

    // Only groups with fresh news; a quiet group drops out of the body entirely.
    $changes = $changes->filter(fn ($c) => $c->hasAny())->values();
    $single = $changes->count() === 1;
    $first = $changes->first();

    // Subject + preheader, photo-led when there is a fresh recap. Derived from the
    // data so this view is self-demonstrating (Nico can mirror it in the Mailable).
    $hasPhotos = $changes->contains(fn ($c) => $c->recentRidesWithPhotos->isNotEmpty());
    $hasPinkVests = $changes->contains(fn ($c) => $c->newPinkVests->isNotEmpty());

    if ($hasPhotos) {
        $subject = $single
            ? "De foto's van de laatste rit in {$first->group->name} staan online"
            : "De foto's van de laatste ritten staan online";
    } elseif ($hasPinkVests) {
        $subject = $single
            ? "Nieuwe roze hesjes in {$first->group->name}"
            : 'Nieuwe roze hesjes bij jouw groepen';
    } else {
        $subject = $single
            ? "Vers nieuws van Kidical Mass {$first->group->name}"
            : 'Vers nieuws van je Kidical Mass groepen';
    }

    $preheader = "De foto's van de laatste rit, wie er nieuw is, en wat er binnenkort op de kalender staat.";

    $introWhere = $single ? 'Kidical Mass '.$first->group->name : 'de groepen die je volgt';

    // Shared inline styles (email clients need them inline, no stylesheet).
    $eyebrow = 'margin:0 0 6px; color:rgba(0,0,0,0.5); font-size:13px; font-weight:700; letter-spacing:1.5px; text-transform:uppercase;';
    $section = 'margin:32px 0 14px; font-size:13px; font-weight:700; letter-spacing:1px; text-transform:uppercase; color:#281a39;';
    $itemTitle = 'margin:0 0 4px; font-size:18px; line-height:1.35; font-weight:700; color:#281a39;';
    $itemMeta = 'margin:0 0 4px; font-size:15px; line-height:1.5; color:#6b6677;';
    $itemLink = 'margin:0 0 24px; font-size:15px; line-height:1.5;';
    $linkStyle = 'color:#1d67cd; font-weight:600; text-decoration:none;';
    $body = 'margin:0 0 18px; font-size:18px; line-height:1.6;';
@endphp

<x-emails.notification
    color="blue"
    :subject="$subject"
    :preheader="$preheader"
    :cta-url="route('activities.index')"
    cta-label="Naar de kalender"
>
    <p style="{{ $eyebrow }}">Kidical Mass · Maandoverzicht</p>
    <h1 style="margin:0 0 28px; color:#281a39; font-size:30px; line-height:1.15; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif; font-weight:800;">Wat beweegt er in de buurt?</h1>

    <p style="{{ $body }}">Hallo!</p>

    <p style="{{ $body }}">
        Hier is wat er deze maand gebeurde bij {{ $introWhere }}.
        De foto's van de laatste rit, wie er nieuw bij is, en wat er binnenkort op de kalender staat.
    </p>

    @foreach ($changes as $change)
        @php
            $group = $change->group;

            // Upcoming: show the soonest few, link the rest to the calendar.
            $upcomingCap = 5;
            $shownUpcoming = $change->upcomingActivities->take($upcomingCap);
            $extraUpcoming = $change->upcomingActivities->count() - $shownUpcoming->count();

            // Pink vests, by first name only ("Sofie, Mehmet en Lars").
            $pinkVestNames = $change->newPinkVests->map(fn ($u) => Str::before($u->name, ' '))->filter()->values();
            $pinkVestList = $pinkVestNames->count() <= 1
                ? $pinkVestNames->first()
                : $pinkVestNames->slice(0, -1)->join(', ').' en '.$pinkVestNames->last();
            $pinkVestVerb = $pinkVestNames->count() === 1 ? 'trok' : 'trokken';
            $pinkVestWhere = $single ? '' : ' in '.$group->name;

            $articles = $change->newArticles->concat($change->updatedArticles)->unique('id')->values();
        @endphp

        @if (! $single)
            <div style="border-top:2px solid #281a39; margin:40px 0 0; padding-top:10px;">
                <p style="margin:0; font-size:22px; line-height:1.2; font-weight:800; color:#281a39;">Kidical Mass {{ $group->name }}</p>
            </div>
        @endif

        {{-- Net gereden, in beeld --}}
        @if ($change->recentRidesWithPhotos->isNotEmpty())
            <p style="{{ $section }}">📸 Net gereden, in beeld</p>

            @foreach ($change->recentRidesWithPhotos->take(2) as $ride)
                <p style="{{ $itemTitle }}">{{ $ride->title_nl }}</p>
                <p style="{{ $itemMeta }}">
                    <time datetime="{{ $ride->begin_date->toIso8601String() }}">{{ RideDate::full($ride->begin_date) }}</time>
                    @if ($ride->location) · {{ $ride->location }} @endif
                </p>

                <table role="presentation" cellpadding="0" cellspacing="0" style="margin:6px 0 10px;">
                    <tr>
                        @foreach ($ride->getMedia('gallery')->take(3) as $photo)
                            <td style="padding:0 8px 0 0;">
                                <a href="{{ route('activities.show', $ride) }}">
                                    <img src="{{ $photo->getFullUrl('thumb') }}" width="150" height="150" alt="Foto van de rit in {{ $group->name }}" style="display:block; width:150px; height:150px; border:0; border-radius:12px; object-fit:cover;">
                                </a>
                            </td>
                        @endforeach
                    </tr>
                </table>

                <p style="{{ $itemLink }}">
                    <a href="{{ route('activities.show', $ride) }}" style="{{ $linkStyle }}">Bekijk alle foto's →</a>
                </p>
            @endforeach
        @endif

        {{-- Binnenkort op de kalender --}}
        @if ($shownUpcoming->isNotEmpty())
            <p style="{{ $section }}">🗓️ Binnenkort op de kalender</p>

            @foreach ($shownUpcoming as $activity)
                <p style="{{ $itemTitle }}">
                    <a href="{{ route('activities.show', $activity) }}" style="color:#281a39; text-decoration:none;">{{ $activity->title_nl }}</a>
                </p>
                <p style="{{ $itemMeta }}">
                    {{ $activity->activity_type->labelNl() }} ·
                    <time datetime="{{ $activity->begin_date->toIso8601String() }}">{{ RideDate::full($activity->begin_date) }}, {{ RideDate::time($activity->begin_date) }}</time>
                    @if ($activity->location) · {{ $activity->location }} @endif
                    @if ($activity->distance) · {{ $activity->distance }} @endif
                </p>
                <p style="{{ $itemLink }}">
                    <a href="{{ route('activities.show', $activity) }}" style="{{ $linkStyle }}">Bekijk de activiteit →</a>
                </p>
            @endforeach

            @if ($extraUpcoming > 0)
                <p style="{{ $itemLink }}">
                    <a href="{{ route('activities.index') }}" style="{{ $linkStyle }}">en nog {{ $extraUpcoming }} {{ $extraUpcoming === 1 ? 'andere activiteit' : 'andere activiteiten' }} →</a>
                </p>
            @endif
        @endif

        {{-- Nieuwe roze hesjes --}}
        @if ($pinkVestNames->isNotEmpty())
            <p style="{{ $section }}">🦺 Nieuwe roze hesjes</p>
            <p style="{{ $body }}">
                {{ $pinkVestList }} {{ $pinkVestVerb }} een roze hesje aan{{ $pinkVestWhere }}. Welkom in het team!
            </p>
        @endif

        {{-- In het nieuws --}}
        @if ($articles->isNotEmpty())
            <p style="{{ $section }}">📰 In het nieuws</p>

            @foreach ($articles as $article)
                <p style="{{ $itemTitle }}">{{ $article->title_nl }}</p>
                <p style="margin:0 0 24px; font-size:15px; line-height:1.6; color:#6b6677;">{{ Str::limit(strip_tags($article->content_nl), 140) }}</p>
            @endforeach
        @endif
    @endforeach
</x-emails.notification>
