{{--
    Privacy & cookies (P-06). One page for both (route `cookies` 301s here).
    Copy is the legal text: GDPR Art. 13 items in tone-of-voice register
    "a notch more serious". Contact email + cookie names come from config so
    the page can never drift from reality. Processor list confirmed by Nico
    (issue #48, 2026-07-07). The Fathom script itself ships with the
    production setup; the copy already describes that state.
--}}
<x-layouts::site title="Privacy & cookies" :description="__('meta.privacy')">

    <x-page-hero
        eyebrow="Praktisch"
        title="Privacy & cookies"
        size="compact">
        <x-slot:lead>
            <p>Kort en eerlijk: dit doen we met jouw gegevens, en dit doen we er niet mee.</p>
        </x-slot:lead>

        <div class="privacy-page max-w-3xl mx-auto flex flex-col gap-12 py-12">

            <section class="flex flex-col gap-4">
                <h2>Wie we zijn</h2>
                <p>Kidical Mass België organiseert vrolijke fietsparades voor kinderen, overal in het land. Wij zijn verantwoordelijk voor de persoonsgegevens die je via deze website met ons deelt. Heb je een vraag over je gegevens? Mail ons op <a href="mailto:{{ config('kidicalmass.contact.email') }}">{{ config('kidicalmass.contact.email') }}</a>.</p>
            </section>

            <section class="flex flex-col gap-6">
                <h2>Welke gegevens we gebruiken, en waarom</h2>
                <p>We verzamelen enkel wat we nodig hebben, en enkel wanneer jij het ons geeft. Je bent nooit verplicht om iets te delen, al kunnen we je zonder e-mailadres natuurlijk niet antwoorden.</p>

                <div class="flex flex-col gap-2">
                    <h3>Als je ons een vraag stuurt</h3>
                    <p>Via de formulieren op deze site (meefietsen als vrijwilliger, een groep starten, partner worden of gewoon een vraag) delen we je naam, e-mailadres, eventueel je telefoonnummer en je bericht. Die gebruiken we alleen om je vraag te beantwoorden en op te volgen. Ze komen terecht bij het kernteam en, als je vraag over een lokale groep gaat, bij het team van die groep. Rechtsgrond: ons gerechtvaardigd belang om te antwoorden wanneer jij ons iets vraagt.</p>
                </div>

                <div class="flex flex-col gap-2">
                    <h3>Als je de nieuwsbrief volgt</h3>
                    <p>Schrijf je je in, dan bewaren we je e-mailadres en de groepen die je wil volgen in MailerLite, onze nieuwsbriefdienst. Je krijgt eerst een bevestigingsmail; pas als je daarin klikt, sta je op de lijst. Uitschrijven kan altijd via de link onderaan elke mail. Rechtsgrond: jouw toestemming, die je dus ook altijd weer kan intrekken.</p>
                </div>

                <div class="flex flex-col gap-2">
                    <h3>Als je een locatie kiest</h3>
                    <p>Kies je op de kalender of bij de lokale groepen een gemeente, dan onthouden we die keuze in een cookie op jouw toestel. Die locatie verlaat je browser niet: wij slaan ze niet op onze servers op.</p>
                </div>

                <div class="flex flex-col gap-2">
                    <h3>Als je vrijwilliger bent</h3>
                    <p>Vrijwilligers krijgen op uitnodiging een account met naam en e-mailadres, zolang ze actief zijn bij hun groep. Er is geen publieke registratie.</p>
                </div>

                <div class="flex flex-col gap-2">
                    <h3>Technische gegevens</h3>
                    <p>Zoals elke website verwerken we kort wat technische gegevens (zoals je IP-adres en browsertype) in sessies en logbestanden, om de site veilig en werkend te houden. We doen niet aan profilering en nemen geen geautomatiseerde beslissingen over jou.</p>
                </div>
            </section>

            <section class="flex flex-col gap-6">
                <h2>Met wie we gegevens delen</h2>
                <p>Met zo weinig mogelijk mensen. Lokale teams zien enkel de aanvragen voor hun eigen groep. We verkopen of verhuren je gegevens nooit, aan niemand.</p>
                <p>Een handvol diensten verwerkt gegevens in onze opdracht. We kozen ze zorgvuldig, zo dicht mogelijk bij huis:</p>

                <dl class="flex flex-col gap-5">
                    <div class="flex flex-col gap-1">
                        <dt>Hosting en back-ups</dt>
                        <dd>De site draait op een server van Hetzner in Duitsland. Back-ups bewaren we bij Akamai in Frankrijk. Alles blijft binnen de Europese Economische Ruimte.</dd>
                    </div>
                    <div class="flex flex-col gap-1">
                        <dt>E-mail</dt>
                        <dd>Mails vanuit de site (zoals de bevestiging van je vraag) versturen we via Postmark, een Amerikaanse dienst die gecertificeerd is onder het EU-US Data Privacy Framework. De nieuwsbrief verzenden we met MailerLite, een Europese dienst die je gegevens binnen de EU bewaart.</dd>
                    </div>
                    <div class="flex flex-col gap-1">
                        <dt>Serverbeheer en monitoring</dt>
                        <dd>Laravel Forge beheert de server, zonder rechtstreekse toegang tot je gegevens. Fouten sporen we op met Flare en de beschikbaarheid bewaken we met Oh Dear, twee Belgische diensten binnen de EER.</dd>
                    </div>
                </dl>
            </section>

            <section class="flex flex-col gap-4">
                <h2>Hoe lang we gegevens bewaren</h2>
                <p>Formulierinzendingen verwijderen we uiterlijk 12 maanden nadat je vraag is afgehandeld, en sowieso uiterlijk 24 maanden na ontvangst. Je nieuwsbriefgegevens bewaren we tot je je uitschrijft. Vrijwilligersaccounts verwijderen we wanneer iemand stopt.</p>
            </section>

            <section class="flex flex-col gap-4">
                <h2>Jouw rechten</h2>
                <p>Je mag altijd weten welke gegevens we van jou hebben. Je kan ze laten verbeteren of verwijderen, de verwerking laten beperken, bezwaar maken of je toestemming intrekken. Eén mailtje naar <a href="mailto:{{ config('kidicalmass.contact.email') }}">{{ config('kidicalmass.contact.email') }}</a> volstaat; we antwoorden binnen de 30 dagen.</p>
                <p>Kom je er met ons niet uit, dan kan je terecht bij de Gegevensbeschermingsautoriteit via <a href="https://www.gegevensbeschermingsautoriteit.be" rel="noopener">gegevensbeschermingsautoriteit.be</a>.</p>
            </section>

            <section class="flex flex-col gap-4">
                <h2>Foto's op onze ritten</h2>
                <p>Op onze ritten worden foto's en video's gemaakt. Die gebruiken we om te tonen hoe leuk samen fietsen is: op deze site, in de nieuwsbrief en op sociale media. We gaan daar zorgvuldig mee om, zeker met beelden van kinderen. Sta jij of je kind herkenbaar op een foto die je liever niet online ziet? Mail ons en we halen de foto weg.</p>
            </section>

            <section class="privacy-cookies flex flex-col gap-6">
                <h2>Cookies</h2>
                <p>Deze site gebruikt geen tracking- of advertentiecookies. Daarom zie je hier ook geen cookiebanner. De cookies die we wel zetten, doen gewoon hun werk:</p>

                <dl class="flex flex-col gap-5">
                    <div class="flex flex-col gap-1">
                        <dt><code>{{ config('session.cookie') }}</code> en <code>XSRF-TOKEN</code></dt>
                        <dd>Houden je bezoek en de formulieren veilig aan de praat. Verdwijnen na 2 uur.</dd>
                    </div>
                    <div class="flex flex-col gap-1">
                        <dt><code>{{ config('location.cookie') }}</code></dt>
                        <dd>Onthoudt de gemeente die je zelf koos bij de kalender of de lokale groepen. Blijft 1 jaar, en enkel op jouw toestel.</dd>
                    </div>
                    <div class="flex flex-col gap-1">
                        <dt><code>roze_welcome_*</code></dt>
                        <dd>Toont ingelogde vrijwilligers eenmalig een welkomstblok. Blijft 90 dagen.</dd>
                    </div>
                </dl>

                <p>Enkele externe diensten maken de site mee mogelijk. De video op de homepage laden we via youtube-nocookie.com, de privacyvriendelijke variant zonder trackingcookies. De kaarten tonen OpenStreetMap-tegels via CARTO, en de lettertypes komen van Bunny Fonts, dat geen cookies zet. Wanneer je browser die beelden ophaalt, ziet die dienst je IP-adres. Meer laten we niet door.</p>
                <p>Bezoekersaantallen meten we met Fathom Analytics, een privacyvriendelijke teller die geen cookies zet en geen persoonsgegevens bewaart. Ook daarom kan die cookiebanner hier weg blijven.</p>
            </section>

            <section class="flex flex-col gap-4">
                <h2>Vragen of wijzigingen</h2>
                <p>Verandert er iets aan hoe we met gegevens omgaan, dan passen we deze pagina aan. Laatst bijgewerkt op <time datetime="2026-07-07">7 juli 2026</time>.</p>
            </section>

        </div>

    </x-page-hero>
</x-layouts::site>
