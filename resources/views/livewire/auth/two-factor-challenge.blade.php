<x-layouts::auth :title="__('auth.two_factor_title')">
    <div
        class="relative w-full h-auto"
        x-cloak
        x-data="{
            showRecoveryInput: @js($errors->has('recovery_code')),
            code: '',
            recovery_code: '',
            toggleInput() {
                this.showRecoveryInput = !this.showRecoveryInput;

                this.code = '';
                this.recovery_code = '';

                $dispatch('clear-2fa-auth-code');

                $nextTick(() => {
                    this.showRecoveryInput
                        ? this.$refs.recovery_code?.focus()
                        : $dispatch('focus-2fa-auth-code');
                });
            },
        }"
    >
        <p class="auth-page__intro" x-show="!showRecoveryInput">{{ __('auth.two_factor_intro_code') }}</p>
        <p class="auth-page__intro" x-show="showRecoveryInput">{{ __('auth.two_factor_intro_recovery') }}</p>

        <form method="POST" action="{{ route('two-factor.login.store') }}">
            @csrf

            <div class="space-y-5 text-center">
                <div x-show="!showRecoveryInput">
                    <div class="flex items-center justify-center my-5">
                        <flux:otp
                            x-model="code"
                            length="6"
                            name="code"
                            label="OTP Code"
                            label:sr-only
                            class="mx-auto"
                         />
                    </div>
                </div>

                <div x-show="showRecoveryInput">
                    <div class="my-5">
                        <flux:input
                            type="text"
                            name="recovery_code"
                            x-ref="recovery_code"
                            x-bind:required="showRecoveryInput"
                            autocomplete="one-time-code"
                            x-model="recovery_code"
                        />
                    </div>

                    @error('recovery_code')
                        <flux:text color="red">
                            {{ $message }}
                        </flux:text>
                    @enderror
                </div>

                <x-cta-button type="submit" variant="yellow">
                    {{ __('auth.two_factor_button') }}
                </x-cta-button>
            </div>

            <div class="mt-5 space-x-0.5 text-sm leading-5 text-center">
                <span class="opacity-50">{{ __('auth.two_factor_or') }}</span>
                <div class="inline font-medium underline cursor-pointer opacity-80">
                    <span x-show="!showRecoveryInput" @click="toggleInput()">{{ __('auth.two_factor_use_recovery') }}</span>
                    <span x-show="showRecoveryInput" @click="toggleInput()">{{ __('auth.two_factor_use_code') }}</span>
                </div>
            </div>
        </form>
    </div>
</x-layouts::auth>
