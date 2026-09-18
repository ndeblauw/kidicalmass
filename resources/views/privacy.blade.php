{{--
    Privacy & cookies (P-06). One page for both (route `cookies` 301s here).
    Copy is the legal text: GDPR Art. 13 items in tone-of-voice register
    "a notch more serious". Contact email + cookie names come from config so
    the page can never drift from reality. Processor list confirmed by Nico
    (issue #48, 2026-07-07). The Fathom script itself ships with the
    production setup; the copy already describes that state.
--}}
@php($dpaUrl = app()->getLocale() === 'fr' ? 'https://www.autoriteprotectiondonnees.be/citoyen' : 'https://www.gegevensbeschermingsautoriteit.be')
@php($updatedOn = app()->getLocale() === 'fr' ? '7 juillet 2026' : '7 juli 2026')
<x-layouts::site :title="__('privacy.title')" :description="__('meta.privacy')">

    <x-page-hero
        :eyebrow="__('privacy.hero.eyebrow')"
        :title="__('privacy.hero.title')"
        size="compact">
        <x-slot:lead>
            <p>{{ __('privacy.hero.lead') }}</p>
        </x-slot:lead>

        <div class="privacy-page max-w-3xl mx-auto flex flex-col gap-12 py-12">

            <section class="flex flex-col gap-4">
                <h2>{{ __('privacy.who.title') }}</h2>
                <p>{!! __('privacy.who.body', ['email' => '<a href="mailto:'.config('kidicalmass.contact.email').'">'.config('kidicalmass.contact.email').'</a>']) !!}</p>
            </section>

            <section class="flex flex-col gap-6">
                <h2>{{ __('privacy.data.title') }}</h2>
                <p>{{ __('privacy.data.intro') }}</p>

                <div class="flex flex-col gap-2">
                    <h3>{{ __('privacy.data.request.title') }}</h3>
                    <p>{{ __('privacy.data.request.body') }}</p>
                </div>

                <div class="flex flex-col gap-2">
                    <h3>{{ __('privacy.data.newsletter.title') }}</h3>
                    <p>{{ __('privacy.data.newsletter.body') }}</p>
                </div>

                <div class="flex flex-col gap-2">
                    <h3>{{ __('privacy.data.location.title') }}</h3>
                    <p>{{ __('privacy.data.location.body') }}</p>
                </div>

                <div class="flex flex-col gap-2">
                    <h3>{{ __('privacy.data.volunteer.title') }}</h3>
                    <p>{{ __('privacy.data.volunteer.body') }}</p>
                </div>

                <div class="flex flex-col gap-2">
                    <h3>{{ __('privacy.data.technical.title') }}</h3>
                    <p>{{ __('privacy.data.technical.body') }}</p>
                </div>
            </section>

            <section class="flex flex-col gap-6">
                <h2>{{ __('privacy.sharing.title') }}</h2>
                <p>{{ __('privacy.sharing.intro_1') }}</p>
                <p>{{ __('privacy.sharing.intro_2') }}</p>

                <dl class="flex flex-col gap-5">
                    <div class="flex flex-col gap-1">
                        <dt>{{ __('privacy.sharing.hosting.title') }}</dt>
                        <dd>{{ __('privacy.sharing.hosting.body') }}</dd>
                    </div>
                    <div class="flex flex-col gap-1">
                        <dt>{{ __('privacy.sharing.email.title') }}</dt>
                        <dd>{{ __('privacy.sharing.email.body') }}</dd>
                    </div>
                    <div class="flex flex-col gap-1">
                        <dt>{{ __('privacy.sharing.server.title') }}</dt>
                        <dd>{{ __('privacy.sharing.server.body') }}</dd>
                    </div>
                </dl>
            </section>

            <section class="flex flex-col gap-4">
                <h2>{{ __('privacy.retention.title') }}</h2>
                <p>{{ __('privacy.retention.body') }}</p>
            </section>

            <section class="flex flex-col gap-4">
                <h2>{{ __('privacy.rights.title') }}</h2>
                <p>{!! __('privacy.rights.body', ['email' => '<a href="mailto:'.config('kidicalmass.contact.email').'">'.config('kidicalmass.contact.email').'</a>']) !!}</p>
                <p>{!! __('privacy.rights.authority', ['link' => '<a href="'.$dpaUrl.'" rel="noopener" target="_blank">'.($dpaUrl === 'https://www.gegevensbeschermingsautoriteit.be' ? 'gegevensbeschermingsautoriteit.be' : 'autoriteprotectiondonnees.be').'</a>']) !!}</p>
            </section>

            <section class="flex flex-col gap-4">
                <h2>{{ __('privacy.photos.title') }}</h2>
                <p>{{ __('privacy.photos.body') }}</p>
            </section>

            <section class="privacy-cookies flex flex-col gap-6">
                <h2>{{ __('privacy.cookies.title') }}</h2>
                <p>{{ __('privacy.cookies.intro') }}</p>

                <dl class="flex flex-col gap-5">
                    <div class="flex flex-col gap-1">
                        <dt><code>{{ config('session.cookie') }}</code> en <code>XSRF-TOKEN</code></dt>
                        <dd>{{ __('privacy.cookies.session.body') }}</dd>
                    </div>
                    <div class="flex flex-col gap-1">
                        <dt><code>{{ config('location.cookie') }}</code></dt>
                        <dd>{{ __('privacy.cookies.location.body') }}</dd>
                    </div>
                    <div class="flex flex-col gap-1">
                        <dt><code>roze_welcome_*</code></dt>
                        <dd>{{ __('privacy.cookies.roze.body') }}</dd>
                    </div>
                </dl>

                <p>{{ __('privacy.cookies.services') }}</p>
                <p>{{ __('privacy.cookies.analytics') }}</p>
            </section>

            <section class="flex flex-col gap-4">
                <h2>{{ __('privacy.updates.title') }}</h2>
                <p>{!! __('privacy.updates.body', ['date' => '<time datetime="2026-07-07">'.$updatedOn.'</time>']) !!}</p>
            </section>

        </div>

    </x-page-hero>
</x-layouts::site>
