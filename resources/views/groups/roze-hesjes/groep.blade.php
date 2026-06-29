<x-roze-hub :group="$group" active="groep" :is-captain="$isCaptain" :show-welcome="$showWelcome" :beheer-url="$beheerUrl" :own-heading="true">
    @php
        $gemeente = trim((string) preg_replace('/^\s*kidical\s+mass\s+/i', '', $group->name));
        $gemeente = $gemeente !== '' ? $gemeente : $group->name;
    @endphp

    {{-- 4 · DE ROZE HESJES — the full roster (replaces the public kapiteins section).
         Everyone is visible to fellow hesjes, regardless of their public opt-in. --}}
    @php
        // Warm per-person avatar accents (cycled), all dark enough for white initials.
        $avatarAccents = ['--color-kidical-red', '--color-kidical-violet', '--color-kidical-blue', '--color-kidical-coral'];
    @endphp

    <section id="de-roze-hesjes">
        <h1 class="roze-hub-title">De roze hesjes van {{ $gemeente }}</h1>
        <p class="roze-hub-lead">De mensen achter de ritten in {{ $gemeente }}. Zij staan ook voor jou klaar.</p>
        <ul role="list" class="roze-roster">
            @forelse ($roster as $member)
                @php
                    $roleLabel = match ($member->pivot->role) {
                        'captain' => 'Kapitein',
                        'pinkvest' => 'Roze hesje',
                        default => 'Geïnteresseerd',
                    };
                @endphp
                <li class="roze-roster__member">
                    <span class="roze-roster__avatar" style="--avatar-accent: var({{ $avatarAccents[$loop->index % count($avatarAccents)] }});" aria-hidden="true">{{ $member->initials() }}</span>
                    <div class="min-w-0">
                        <span class="roze-roster__name roze-row-title">{{ $member->name }}</span>
                        <span @class(['roze-roster__role', 'roze-roster__role--lead' => $member->pivot->role === 'captain'])>{{ $roleLabel }}</span>
                    </div>
                    @if ($member->pivot->created_at && $member->pivot->created_at->greaterThan($newMemberCutoff))
                        <span class="roze-roster__new">Nieuw</span>
                    @endif
                </li>
            @empty
                <li class="roze-roster__member">
                    <span class="roze-roster__role">Hier komt straks het team van {{ $gemeente }}.</span>
                </li>
            @endforelse
        </ul>
    </section>
</x-roze-hub>
