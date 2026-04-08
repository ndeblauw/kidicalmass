<footer>
    <div class="container mx-auto px-4 py-12">
        <div class="grid md:grid-cols-2 gap-12">
            <!-- Left Column: Logo and Sponsors -->
            <div class="flex flex-col items-center">
                <img src="{{asset('img/footer-logo.avif')}}" alt="Kidical Mass Logo" class="max-w-xs w-full h-auto" />

                <div class="space-y-4">
                    <img src="{{asset('img/sponsors/bm-nl.avif')}}" class="h-24" alt="Met de steun van Brussel Mobiliteit"/>
                    <img src="{{asset('img/sponsors/bm-fr.avif')}}" class="h-24" alt="Avec le soutien de Bruxelles Mobilité"/>
                </div>
            </div>

            <!-- Right Column: Contact Information -->
            <div class="space-y-6">
                <!-- Call to Action -->
                <div>
                    <flux:text>Engagez-vous en tant que accompagnateur/co-organisateur !</flux:text>
                    <flux:text>Wij zoeken nog begeleiders en lokale trekkers!</flux:text>
                    <flux:text>Join your local group - mail: <flux:link href="mailto:bike@kidicalmass.be">bike@kidicalmass.be</flux:link></flux:text>
                </div>

                <!-- Donation Information -->
                <div>
                    <flux:text>Want to do a donation ?</flux:text>
                    <flux:text>Kidical Mass Belgium (vzw) - BE72 8919 4405 3116 (VDK)</flux:text>
                </div>

                <!-- Sponsor Contact -->
                <div>
                    <flux:text>Want to be a sponsor ? <flux:link href="mailto:contact@kidicalmass.brussels">contact@kidicalmass.brussels</flux:link></flux:text>
                    <div class="flex flex-wrap gap-2">
                        <flux:link href="#">Sponsorformulas</flux:link>
                        <span>-</span>
                        <flux:link href="#">Sponsor & partner charter</flux:link>
                    </div>
                </div>

                <!-- Press Contact -->
                <div>
                    <flux:text>Contact Presse/Pers</flux:text>
                    <flux:text>Kidical Mass Belgium</flux:text>
                    <flux:text>Leticia Sere - coordination</flux:text>
                    <flux:text>Cecilia Pagola - PR - <flux:link href="mailto:cecilia@kidicalmass.be">cecilia@kidicalmass.be</flux:link></flux:text>
                    <flux:text>0495 81 27 95 - <flux:link href="mailto:bike@kidicalmass.be">bike@kidicalmass.be</flux:link></flux:text>
                </div>

                <!-- Social Media -->
                <div>
                    <flux:text>Follow us on:</flux:text>
                    <div class="flex space-x-3">
                        <a href="#" aria-label="Instagram">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                        <a href="#" aria-label="Facebook">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg>
                        </a>
                        <a href="#" aria-label="LinkedIn">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <flux:separator class="mt-8" />

        <!-- Quick Links Section -->
        <div class="mt-8">
            <div class="grid md:grid-cols-4 gap-8">
                <!-- About -->
                <div>
                    <h3 class="mb-4">
                        Kidical Mass Belgium
                    </h3>
                    <flux:text>Safe, fun, and accessible cycling for families and children. Join us in creating a better future for our cities!</flux:text>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><flux:link href="{{ route('home') }}">Home</flux:link></li>
                        <li><flux:link href="{{ route('groups.index') }}">Groups</flux:link></li>
                        <li><flux:link href="{{ route('articles.index') }}">Articles</flux:link></li>
                        <li><flux:link href="{{ route('activities.index') }}">Activities</flux:link></li>
                    </ul>
                </div>

                <!-- Get Involved -->
                <div>
                    <h3 class="mb-4">Get Involved</h3>
                    <ul class="space-y-2">
                        <li><flux:link href="#">Join a Group</flux:link></li>
                        <li><flux:link href="#">Organize an Event</flux:link></li>
                        <li><flux:link href="#">Volunteer</flux:link></li>
                        <li><flux:link href="#">Donate</flux:link></li>
                    </ul>
                </div>

                <!-- More Info -->
                <div>
                    <h3 class="mb-4">More Info</h3>
                    <ul class="space-y-2">
                        <li><flux:link href="mailto:info@kidicalmass.be">info@kidicalmass.be</flux:link></li>
                    </ul>
                </div>
            </div>
        </div>

        <flux:separator class="mt-8" />

        <!-- Bottom Bar -->
        <div class="mt-8 text-center">
            <flux:text>&copy; {{ date('Y') }} Kidical Mass Belgium. All rights reserved.</flux:text>
            <flux:text class="mt-2">
                <flux:link href="#">Privacy Policy</flux:link> •
                <flux:link href="#">Terms of Service</flux:link> •
                <flux:link href="#">Contact Us</flux:link>
            </flux:text>
        </div>
    </div>
</footer>
