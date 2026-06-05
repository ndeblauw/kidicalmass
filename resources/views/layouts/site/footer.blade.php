<footer class="site-footer">
    <div class="container mx-auto px-4">

        <div class="site-footer__main">

            {{-- Brand + persistent membership CTA --}}
            <div>
                <img src="{{ asset('img/footer-logo.avif') }}" alt="Kidical Mass Belgium" class="site-footer__logo">
                <p class="site-footer__tagline">{{ __('footer.tagline') }}</p>
                <a href="{{ route('membership') }}" class="site-footer__cta">{{ __('footer.membership_cta') }}</a>
            </div>

            {{-- Discover — mirrors the main nav --}}
            <div>
                <h3 class="site-footer__col-title">{{ __('footer.discover') }}</h3>
                <ul class="site-footer__links">
                    <li><a href="{{ route('activities.index') }}">{{ __('nav.events') }}</a></li>
                    <li><a href="{{ route('groups.index') }}">{{ __('nav.chapters') }}</a></li>
                    <li><a href="{{ route('getting-started') }}">{{ __('nav.getting_started') }}</a></li>
                    <li><a href="{{ route('volunteer') }}">{{ __('nav.help_out') }}</a></li>
                </ul>
            </div>

            {{-- About — mirrors the About dropdown --}}
            <div>
                <h3 class="site-footer__col-title">{{ __('footer.about') }}</h3>
                <ul class="site-footer__links">
                    <li><a href="{{ route('about.mission') }}">{{ __('nav.mission') }}</a></li>
                    <li><a href="{{ route('about.vision') }}">{{ __('nav.vision') }}</a></li>
                    <li><a href="{{ route('about.organisation') }}">{{ __('nav.organisation') }}</a></li>
                    <li><a href="{{ route('articles.index') }}">{{ __('nav.news') }}</a></li>
                    <li><a href="{{ route('about.press') }}">{{ __('nav.press') }}</a></li>
                    <li><a href="{{ route('about.partners') }}">{{ __('nav.partners') }}</a></li>
                </ul>
            </div>

            {{-- Follow --}}
            <div>
                <h3 class="site-footer__col-title">{{ __('footer.follow_us') }}</h3>
                <div class="site-footer__social">
                    <a href="https://www.instagram.com/kidicalmass.belgium/" target="_blank" rel="noopener noreferrer" aria-label="Instagram" class="site-footer__social-link">
                        <svg fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    </a>
                    <a href="https://www.facebook.com/Kidicalmass.brussels" target="_blank" rel="noopener noreferrer" aria-label="Facebook" class="site-footer__social-link">
                        <svg fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg>
                    </a>
                </div>
            </div>

        </div>

        {{-- Funder acknowledgment — quiet, site-wide --}}
        <div class="site-footer__funder">
            <span>{{ __('partners.funder_credit') }}</span>
            <img src="{{ asset('img/sponsors/bm-nl.avif') }}" alt="Mede mogelijk gemaakt door Brussel Mobiliteit" class="site-footer__funder-logo">
        </div>

        {{-- Bottom bar — utilities --}}
        <div class="site-footer__bottom">
            <span>&copy; {{ date('Y') }} Kidical Mass Belgium</span>
            <span>{{ __('footer.website_by') }} <a href="https://bluepundit.eu/" target="_blank" rel="noopener noreferrer">Blue Pundit</a> &amp; <a href="https://frederikvincx.com/" target="_blank" rel="noopener noreferrer">Impact Studio</a></span>
            <ul class="site-footer__bottom-links">
                <li><a href="{{ route('contact') }}">{{ __('common.contact') }}</a></li>
                <li><a href="{{ route('privacy') }}">{{ __('common.privacy_cookies') }}</a></li>
                <li><a href="{{ route('login') }}">{{ __('nav.login') }}</a></li>
            </ul>
        </div>

    </div>
</footer>
