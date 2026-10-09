<x-layouts::auth :title="__('Register')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Create an account')" :description="__('Only Software Engineering students with a university email can register.')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Name -->
            <flux:input
                name="name"
                :label="__('Full name')"
                :value="old('name')"
                type="text"
                required
                autofocus
                autocomplete="name"
                :placeholder="__('Full name')"
            />

            <!-- Registration No. -->
            <flux:input
                name="registration_no"
                :label="__('Registration no.')"
                :value="old('registration_no')"
                type="text"
                required
                autocomplete="off"
                placeholder="SU92-BSSEM-F22-171"
            />

            <!-- Email Address -->
            <flux:input
                name="email"
                :label="__('University email')"
                :value="old('email')"
                type="email"
                required
                autocomplete="email"
                placeholder="name@superior.edu.pk"
            />

            <!-- Program (fixed) -->
            <flux:input
                :label="__('Program')"
                :value="config('students.default_program')"
                disabled
            />

            <!-- Current Semester -->
            <flux:select
                name="current_semester"
                :label="__('Current semester')"
                :placeholder="__('Select your current semester')"
                required
            >
                @foreach (range(1, 8) as $semester)
                    <flux:select.option :value="(string) $semester" :selected="old('current_semester') == $semester">
                        {{ $semester }}
                    </flux:select.option>
                @endforeach
            </flux:select>

            <!-- Password -->
            <flux:input
                name="password"
                :label="__('Password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Password')"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable
            />

            <!-- Confirm Password -->
            <flux:input
                name="password_confirmation"
                :label="__('Confirm password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Confirm password')"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable
            />

            <div class="flex items-center justify-end">
                <flux:button type="submit" variant="primary" class="w-full" data-test="register-user-button">
                    {{ __('Create account') }}
                </flux:button>
            </div>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-muted">
            <span>{{ __('Already have an account?') }}</span>
            <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
        </div>
    </div>
</x-layouts::auth>
