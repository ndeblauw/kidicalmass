<flux:card class="space-y-6">
    <h2>Contact Us</h2>

    @if($submitted)
        <flux:callout variant="success" icon="check-circle">
            <flux:callout.heading>Thank you for your message!</flux:callout.heading>
            <flux:callout.text>We'll get back to you as soon as possible.</flux:callout.text>
        </flux:callout>
    @endif

    <form wire:submit="submit" class="grid grid-cols-2 gap-6">
        <div class="space-y-6">
            <flux:input label="Name" type="text" wire:model="name" required />
            <flux:input label="Email" type="email" wire:model="email" required />
            <flux:input label="Phone" type="tel" wire:model="phone" />
        </div>

        <flux:textarea label="Message" wire:model="message" rows="8" required />

        <!-- Honeypot Field (Hidden) -->
        <div class="hidden" aria-hidden="true">
            <label for="website">Website</label>
            <input type="text" id="website" wire:model="website" tabindex="-1" autocomplete="off">
        </div>

        <div class="col-span-2">
            <flux:button type="submit" variant="primary" class="w-full">Send Message</flux:button>
        </div>
    </form>
</flux:card>
