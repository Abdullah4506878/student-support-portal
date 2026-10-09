<x-layouts::auth :title="__('Log in')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Welcome back')" :description="__('Log in with your university email to continue.')" />

        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-[18px]">
            @csrf

            <div
                x-data="{
                    touched: false,
                    valid: true,
                    check(value) {
                        this.valid = value === '' || /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
                    },
                }"
            >
                <flux:input
                    name="email"
                    :label="__('University email')"
                    :value="old('email')"
                    type="email"
                    required
                    autofocus
                    autocomplete="email"
                    placeholder="name@superior.edu.pk"
                    x-on:blur="touched = true; check($event.target.value)"
                    x-on:input="if (touched) check($event.target.value)"
                />
                <p x-show="touched && !valid" class="mt-1 text-sm text-red-600">{{ __('Enter a valid email address.') }}</p>
            </div>

            <flux:input
                name="password"
                :label="__('Password')"
                type="password"
                required
                autocomplete="current-password"
                :placeholder="__('Enter your password')"
                viewable
            />

            <flux:checkbox name="remember" :label="__('Keep me signed in on this device')" :checked="old('remember')" />

            <flux:button variant="primary" type="submit" class="w-full" data-test="login-button">
                {{ __('Log in') }}
            </flux:button>

            @if (Route::has('password.request'))
                <flux:link class="self-center text-sm font-semibold" :href="route('password.request')" wire:navigate>
                    {{ __('Forgot your password?') }}
                </flux:link>
            @endif
        </form>

        <div class="flex items-center gap-3 text-[13px] text-placeholder">
            <span class="h-px flex-1 bg-border-soft"></span>
            {{ __('New to the portal?') }}
            <span class="h-px flex-1 bg-border-soft"></span>
        </div>

        <flux:button :href="route('register')" variant="outline" class="w-full" wire:navigate>
            {{ __('Create a student account') }}
        </flux:button>

        <p class="m-0 text-center text-[13px] leading-relaxed text-muted">
            {{ __('Only Software Engineering students with an @superior.edu.pk email can register. Trouble logging in? Contact the SE Admin Office.') }}
        </p>
    </div>
</x-layouts::auth>
