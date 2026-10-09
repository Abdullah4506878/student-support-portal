<x-layouts::auth :title="__('Email verification')">
    <div class="flex flex-col items-center gap-6 text-center">
        <span class="flex size-14 shrink-0 items-center justify-center rounded-full bg-plum/10">
            <flux:icon icon="envelope" variant="outline" class="size-7 text-plum" />
        </span>

        <div class="flex flex-col gap-2">
            <flux:heading size="lg">{{ __('Check your university email') }}</flux:heading>
            <flux:text>
                {{ __('We sent a verification link to :email. Click it to activate your account. The link expires in 60 minutes.', ['email' => auth()->user()->email]) }}
            </flux:text>
            <flux:text class="text-subtle">{{ __("Can't find it? Check your spam folder.") }}</flux:text>
        </div>

        @if (session('status') == 'verification-link-sent')
            <flux:text class="font-medium !text-green-600">
                {{ __('A new verification link has been sent.') }}
            </flux:text>
        @endif

        <div class="flex w-full flex-col items-center justify-between gap-3">
            <form method="POST" action="{{ route('verification.send') }}" class="w-full">
                @csrf
                <flux:button type="submit" variant="primary" class="w-full">
                    {{ __('Resend verification email') }}
                </flux:button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <flux:button variant="ghost" type="submit" class="text-sm cursor-pointer" data-test="logout-button">
                    {{ __('Log out') }}
                </flux:button>
            </form>
        </div>
    </div>
</x-layouts::auth>
