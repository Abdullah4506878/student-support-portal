<x-layouts::app :title="__('Admin Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <flux:heading size="xl">{{ __('Admin Officer Dashboard') }}</flux:heading>
        <flux:text>{{ __('Your department\'s applications will appear here.') }}</flux:text>
    </div>
</x-layouts::app>
