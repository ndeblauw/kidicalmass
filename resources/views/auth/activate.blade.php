{{--
    Account activation (prototype). Reuses the real auth layout so it reads as the genuine
    "join your chapter" step. One click logs the demo volunteer in and lands them on the
    welcome screen (real invite-token provisioning is the D-12 hand-off, out of scope).
--}}
<x-layouts::auth :title="'Activeer je account'">
    <div class="flex flex-col gap-6">
        <x-auth-header
            :title="'Welkom, '.(\Illuminate\Support\Str::before($volunteer?->name ?? 'fietser', ' ')).'.'"
            :description="'Nog één klik en je staat tussen de roze hesjes van '.$group->name.'.'" />

        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('backstage.activate', $group) }}" class="flex flex-col gap-6">
            @csrf

            <flux:button type="submit" variant="primary" class="w-full">
                Account activeren
            </flux:button>
        </form>

        <p class="text-center text-sm text-zinc-500">
            Een licht account, gewoon zodat je op elk toestel bij je materiaal kan.
        </p>
    </div>
</x-layouts::auth>
