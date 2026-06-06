{{--
    Account activation / set-password (prototype). Reuses the real auth layout so it reads as
    the genuine "set your password" step. Shallow: POST logs the demo volunteer in and lands
    them on the welcome screen (real invite-token provisioning is the D-12 hand-off, out of scope).
--}}
<x-layouts::auth :title="'Activeer je account'">
    <div class="flex flex-col gap-6">
        <x-auth-header
            :title="'Stel je wachtwoord in'"
            :description="'Nog één stap, '.(\Illuminate\Support\Str::before($volunteer?->name ?? 'fietser', ' ')).'. Kies een wachtwoord en je bent binnen.'" />

        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('backstage.activate', $group) }}" class="flex flex-col gap-6">
            @csrf

            <flux:input
                name="password"
                :label="'Wachtwoord'"
                type="password"
                required
                autocomplete="new-password"
                placeholder="Minstens 8 tekens"
                viewable />

            <flux:input
                name="password_confirmation"
                :label="'Herhaal wachtwoord'"
                type="password"
                required
                autocomplete="new-password"
                placeholder="Nog een keer"
                viewable />

            <flux:button type="submit" variant="primary" class="w-full">
                Account activeren
            </flux:button>
        </form>

        <p class="text-center text-sm text-zinc-500">
            Een licht account, gewoon zodat je op elk toestel bij je materiaal kan.
        </p>
    </div>
</x-layouts::auth>
