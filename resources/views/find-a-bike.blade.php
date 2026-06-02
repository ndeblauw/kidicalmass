{{--
    Find a bike — "Geen fiets? Geen probleem"
    Standalone resource page (not in main nav). Reached from the Getting Started FAQ;
    reusable later from event/chapter pages. Wireframe fidelity: structure + NL content +
    semantic HTML, minimal styling. Holds the no-bike provider detail that was lifted off
    Getting Started (2026-06-02) to keep that page's reassure → CTA path clean.
    Content source: docs/wiki/design/30-skeleton/getting-started-content.md
--}}
<x-layouts::site title="Geen fiets? Geen probleem">

    <div class="mx-auto max-w-4xl space-y-10">

        {{-- HEADER --}}
        <header class="space-y-2">
            <h1>Geen fiets? Geen probleem</h1>
            <p>Geen fiets is geen reden om thuis te blijven. Lenen, huren of een abonnement: er is voor elk gezin iets.</p>
        </header>

        {{-- PROVIDERS --}}
        <section class="space-y-4">
            <ul role="list" class="grid gap-6 sm:grid-cols-2">
                <li class="space-y-1">
                    <h2>Kidical Mouse</h2>
                    <p><small>Sommige Brusselse ritten</small></p>
                    <p>Een bakfiets staat klaar aan de start. Stap gewoon op als je zelf geen fiets hebt. Check de pagina van je afdeling of die voor jouw rit beschikbaar is.</p>
                </li>
                <li class="space-y-1">
                    <h2>Loopz</h2>
                    <p><small>Nationaal</small></p>
                    <p>Huur een fiets via een lokale winkelpartner, voor kinderen en volwassenen. Vanaf €6/maand. Met code <strong>KIDICALMASS</strong>: twee maanden gratis.</p>
                    <a href="https://loopz.bike" target="_blank" rel="noopener noreferrer">loopz.bike →</a>
                </li>
                <li class="space-y-1">
                    <h2>Fietsbieb</h2>
                    <p><small>Vlaanderen + Brussel</small></p>
                    <p>Leen een kinderfiets voor het hele jaar (tot 12 jaar) en ruil hem in voor een grotere maat als je kind groeit. €30/jaar (€10 met verhoogde tegemoetkoming) + €20 waarborg.</p>
                    <a href="https://fietsbieb.be" target="_blank" rel="noopener noreferrer">fietsbieb.be →</a>
                </li>
                <li class="space-y-1">
                    <h2>My Kids Bikes</h2>
                    <p><small>Nationaal</small></p>
                    <p>Een abonnement op een kwaliteitsvolle kinderfiets die meegroeit met je kind. Woom-fietsen, prijs volgens maat.</p>
                    <a href="https://mykidsbikes.be" target="_blank" rel="noopener noreferrer">mykidsbikes.be →</a>
                </li>
            </ul>

            <p>
                <small>
                    Ook: <a href="https://cyclo.be" target="_blank" rel="noopener noreferrer">Cyclo</a> (Brussel) verkoopt tweedehandsfietsen aan toegankelijke prijzen.
                    Buiten Brussel zijn Loopz en My Kids Bikes je beste nationale opties, en je lokale afdeling kent misschien iets in de buurt: <a href="{{ route('groups.index') }}">vind je afdeling →</a>
                </small>
            </p>
        </section>

        {{-- BACK TO THE RIDE --}}
        <section class="space-y-3">
            <h2>Een fiets gevonden?</h2>
            <p>
                <flux:button href="{{ route('activities.index') }}" variant="primary" icon-trailing="arrow-right">
                    Vind een rit bij jou in de buurt
                </flux:button>
            </p>
            <p><a href="{{ route('getting-started') }}">← Terug naar Voor het eerst mee</a></p>
        </section>

    </div>

</x-layouts::site>
