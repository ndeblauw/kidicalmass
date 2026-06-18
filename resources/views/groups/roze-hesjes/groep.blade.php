<x-roze-hub :group="$group" active="groep" :is-captain="$isCaptain" :show-welcome="$showWelcome" :beheer-url="$beheerUrl">
    @php
        $gemeente = trim((string) preg_replace('/^\s*kidical\s+mass\s+/i', '', $group->name));
        $gemeente = $gemeente !== '' ? $gemeente : $group->name;
    @endphp

    {{-- 4 · DE ROZE HESJES — the full roster (replaces the public kapiteins section).
         Everyone is visible to fellow hesjes, regardless of their public opt-in. --}}
    <section id="de-roze-hesjes">
        <h2 class="roze-hub-title">De roze hesjes van {{ $gemeente }}</h2>
        <p class="roze-hub-lead">De mensen die mee de ritten dragen. Zij staan ook voor jou klaar.</p>
        <ul role="list" class="roze-roster">
            @foreach ($roster as $member)
                <li class="roze-roster__member">
                    <span class="roze-roster__avatar" aria-hidden="true">{{ $member->initials() }}</span>
                    <div class="min-w-0">
                        <strong class="roze-roster__name roze-row-title">{{ $member->name }}</strong>
                        <span class="roze-roster__role">{{ $member->pivot->role === 'captain' ? 'Kapitein' : 'Roze hesje' }}</span>
                    </div>
                    @if ($member->pivot->created_at && $member->pivot->created_at->greaterThan($newMemberCutoff))
                        <span class="roze-roster__new">Nieuw</span>
                    @endif
                </li>
            @endforeach
        </ul>
    </section>
</x-roze-hub>
