@props(['code', 'title', 'message'])

<x-layouts::auth :title="$title">
    <div class="flex flex-col items-center gap-4 text-center">
        <span class="font-display text-[64px] leading-none font-semibold text-plum">{{ $code }}</span>
        <h2 class="m-0 text-[22px] font-bold text-ink">{{ $title }}</h2>
        <p class="m-0 text-[15px] text-muted">{{ $message }}</p>

        @auth
            <flux:button :href="route('dashboard')" variant="primary">
                {{ __('Go to my dashboard') }}
            </flux:button>
        @else
            <flux:button :href="route('login')" variant="primary">
                {{ __('Go to login') }}
            </flux:button>
        @endauth
    </div>
</x-layouts::auth>
