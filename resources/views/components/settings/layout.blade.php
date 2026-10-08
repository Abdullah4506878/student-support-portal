<div class="flex items-start max-md:flex-col">
    <div class="me-10 w-full pb-4 md:w-[220px]">
        <flux:navlist aria-label="{{ __('Settings') }}">
            <flux:navlist.item :href="route('profile.edit')" wire:navigate>{{ __('Profile') }}</flux:navlist.item>
            <flux:navlist.item :href="route('security.edit')" wire:navigate>{{ __('Security') }}</flux:navlist.item>
        </flux:navlist>
    </div>

    <flux:separator class="md:hidden" />

    <div class="flex-1 self-stretch max-md:pt-6">
        <h2 class="m-0 text-[17px] font-bold text-ink">{{ $heading ?? '' }}</h2>
        <p class="m-0 mt-1 text-sm text-muted">{{ $subheading ?? '' }}</p>

        <div class="mt-5 w-full max-w-lg rounded-[14px] border border-border bg-white p-6">
            {{ $slot }}
        </div>
    </div>
</div>
