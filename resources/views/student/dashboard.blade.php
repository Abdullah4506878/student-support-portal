<x-layouts::app :title="__('Student Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <flux:heading size="xl">{{ __('Student Dashboard') }}</flux:heading>
        <flux:text>{{ __('Your applications and announcements will appear here.') }}</flux:text>
    </div>
</x-layouts::app>
