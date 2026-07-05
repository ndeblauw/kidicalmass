{{--
    Contact (national, P-05) — the A3a arrangement won the three-round
    prototype (2026-07-05): topic pills at the top of the form, the secondary
    blocks (liever mailen, meehelpen-redirect) as a sticky info-card sidebar,
    the /about/press composition. No hero lead: the pills label does the
    triage explanation. Copy: lang/nl/contact.php.
    Brief: docs/wiki/design/30-skeleton/contact.md
--}}
<x-layouts::site :title="__('contact.title')" :description="__('meta.contact')">

    <x-page-hero
        size="compact"
        :eyebrow="__('contact.hero_eyebrow')"
        :title="__('contact.hero_title')">

        <div class="contact-arrange grid items-start gap-10 md:grid-cols-[1.5fr_1fr] md:gap-16">
            <section id="schrijf-ons" aria-labelledby="contact-form-title">
                <h2 id="contact-form-title" class="sr-only">{{ __('contact.form_heading') }}</h2>
                <livewire:contact-form-component />
            </section>

            <aside class="flex flex-col gap-4 md:sticky md:top-28">
                <x-info-card :label="__('contact.mail_card_title')">
                    <p>{{ __('contact.mail_card_body') }}</p>
                    <a href="mailto:{{ config('kidicalmass.contact.email') }}" class="info-card__link">{{ config('kidicalmass.contact.email') }}</a>
                    <p class="info-card__note">{{ __('contact.mail_card_call') }} <a href="tel:{{ config('kidicalmass.contact.phone_e164') }}">{{ config('kidicalmass.contact.phone') }}</a>.</p>
                </x-info-card>

                <x-info-card :label="__('contact.volunteer_card_title')">
                    <p>{{ __('contact.volunteer_card_body') }}</p>
                    <a href="{{ route('volunteer') }}" class="info-card__link">{{ __('contact.volunteer_card_link') }}</a>
                </x-info-card>
            </aside>
        </div>

    </x-page-hero>

</x-layouts::site>
