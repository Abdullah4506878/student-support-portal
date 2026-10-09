<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Create an account')" :description="__('Only Software Engineering students with a university email can register.')" />

    <form wire:submit="register" class="flex flex-col gap-6">
        <!-- Name -->
        <flux:input
            wire:model.blur="name"
            x-on:input="@if ($errors->has('name')) $wire.set('name', $event.target.value) @endif"
            :label="__('Full name')"
            type="text"
            required
            autofocus
            autocomplete="name"
            :placeholder="__('Full name')"
        />

        <!-- Program -->
        <flux:select wire:model.live="program" :label="__('Program')" :placeholder="__('Select your program')" required>
            @foreach (config('students.programs') as $code => $label)
                <flux:select.option :value="$code">{{ $label }}</flux:select.option>
            @endforeach
        </flux:select>

        <!-- Registration No. -->
        <div class="flex flex-col gap-1">
            <flux:input
                wire:model.live.debounce.300ms="registration_no"
                :label="__('Registration no.')"
                type="text"
                required
                autocomplete="off"
                placeholder="SU92-BSSEM-F22-171"
            />
            <span class="text-xs text-subtle">{{ __('Format: SU92-BSSEM-F22-171') }}</span>
        </div>

        <!-- Email Address -->
        <flux:input
            wire:model.blur="email"
            x-on:input="@if ($errors->has('email')) $wire.set('email', $event.target.value) @endif"
            :label="__('University email')"
            type="email"
            required
            autocomplete="email"
            placeholder="name@superior.edu.pk"
        />

        <!-- Current Semester -->
        <flux:select wire:model.live="current_semester" :label="__('Current semester')" :placeholder="__('Select your current semester')" required>
            @foreach (range(1, 8) as $semester)
                <flux:select.option :value="(string) $semester">{{ $semester }}</flux:select.option>
            @endforeach
        </flux:select>

        <!-- Password -->
        <flux:input
            wire:model.blur="password"
            x-on:input="@if ($errors->has('password')) $wire.set('password', $event.target.value) @endif"
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
            wire:model.blur="password_confirmation"
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
