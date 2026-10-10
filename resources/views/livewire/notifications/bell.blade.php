<div>
    <flux:dropdown position="bottom" align="end">
        <button
            type="button"
            aria-label="{{ __('Notifications') }}"
            class="{{ $variant === 'light'
                ? 'relative flex size-11 items-center justify-center rounded-xl border border-border text-ink-soft no-underline hover:bg-page'
                : 'relative flex size-11 items-center justify-center rounded-xl text-white no-underline hover:bg-white/10' }}"
        >
            <flux:icon icon="bell" variant="outline" class="size-5" />
            @if ($this->unreadCount > 0)
                <span class="absolute top-1.5 right-1.5 flex size-4.5 min-w-4.5 items-center justify-center rounded-full px-1 text-[10px] font-bold {{ $variant === 'light' ? 'bg-plum text-white' : 'bg-white text-plum-dark' }}">
                    {{ $this->unreadCount > 9 ? '9+' : $this->unreadCount }}
                </span>
            @endif
        </button>

        <flux:menu class="w-80">
            <div class="flex items-center justify-between gap-3 px-3 py-2">
                <flux:heading size="sm">{{ __('Notifications') }}</flux:heading>
                @if ($this->unreadCount > 0)
                    <button type="button" wire:click="markAllAsRead" class="cursor-pointer text-xs font-semibold text-plum-link">
                        {{ __('Mark all as read') }}
                    </button>
                @endif
            </div>

            <flux:menu.separator />

            @forelse ($this->latest as $notification)
                <flux:menu.item as="button" type="button" wire:click="open('{{ $notification->id }}')" class="w-full cursor-pointer">
                    <div class="flex items-start gap-2 py-0.5 text-start">
                        @unless ($notification->read_at)
                            <span class="mt-1.5 size-1.5 shrink-0 rounded-full bg-plum"></span>
                        @endunless
                        <div class="flex flex-col gap-0.5 {{ $notification->read_at ? 'ps-3.5' : '' }}">
                            <span class="text-sm {{ $notification->read_at ? 'text-ink-soft' : 'font-semibold text-ink' }}">{{ $notification->data['message'] ?? '' }}</span>
                            <span class="text-xs text-subtle">{{ $notification->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </flux:menu.item>
            @empty
                <div class="px-3 py-6 text-center text-sm text-subtle">{{ __('No notifications yet.') }}</div>
            @endforelse

            @if ($this->indexRouteName)
                <flux:menu.separator />
                <flux:menu.item :href="route($this->indexRouteName)" wire:navigate>
                    {{ __('View all notifications') }}
                </flux:menu.item>
            @endif
        </flux:menu>
    </flux:dropdown>
</div>
