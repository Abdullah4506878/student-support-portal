@props([
    'notificationsRoute' => null,
])

<flux:header container class="border-b border-plum-dark bg-plum">
    {{ $leading ?? '' }}

    <a href="{{ route('dashboard') }}" wire:navigate>
        <x-superior-logo />
    </a>

    <flux:spacer />

    @if ($notificationsRoute)
        <flux:tooltip content="{{ __('Notifications') }}" position="bottom">
            <flux:navbar.item
                class="h-10! text-white [&>div>svg]:size-5"
                icon="bell"
                :href="Route::has($notificationsRoute) ? route($notificationsRoute) : '#'"
                :label="__('Notifications')"
            />
        </flux:tooltip>
    @endif

    <flux:dropdown position="bottom" align="end">
        <button type="button" class="flex items-center rounded-full ring-2 ring-white/40 hover:ring-white/70" data-test="user-menu-button">
            <flux:avatar size="sm" :name="auth()->user()->name" :initials="auth()->user()->initials()" />
        </button>

        <flux:menu>
            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                <flux:avatar :name="auth()->user()->name" :initials="auth()->user()->initials()" />

                <div class="grid flex-1 text-start text-sm leading-tight">
                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                </div>
            </div>

            <flux:menu.separator />

            <flux:menu.radio.group>
                <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                    {{ __('Settings') }}
                </flux:menu.item>

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item
                        as="button"
                        type="submit"
                        icon="arrow-right-start-on-rectangle"
                        class="w-full cursor-pointer"
                        data-test="logout-button"
                    >
                        {{ __('Log out') }}
                    </flux:menu.item>
                </form>
            </flux:menu.radio.group>
        </flux:menu>
    </flux:dropdown>
</flux:header>
