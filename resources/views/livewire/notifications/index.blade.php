<div class="flex flex-col gap-6 py-7">
    <x-page-header :title="__('Notifications')" :subtitle="__('Updates about your applications.')">
        <x-slot:actions>
            @if ($this->unreadCount > 0)
                <flux:button variant="ghost" wire:click="markAllAsRead">{{ __('Mark all as read') }}</flux:button>
            @endif
        </x-slot:actions>
    </x-page-header>

    @if ($this->notifications->isEmpty())
        <x-empty-state icon="bell" :title="__('No notifications yet')" :description="__('You will see updates about your applications here.')" />
    @else
        <x-panel :title="__('All notifications')">
            <div class="flex flex-col">
                @foreach ($this->notifications as $notification)
                    <div class="flex items-center justify-between gap-3 border-t border-border-soft px-[22px] py-3.5 first:border-t-0 {{ $notification->read_at ? '' : 'bg-page' }}">
                        <button type="button" wire:click="open('{{ $notification->id }}')" class="flex min-w-0 flex-1 cursor-pointer items-start gap-3 text-start">
                            @unless ($notification->read_at)
                                <span class="mt-1.5 size-2 shrink-0 rounded-full bg-plum"></span>
                            @endunless
                            <div class="flex flex-col gap-0.5 {{ $notification->read_at ? 'ps-5' : '' }}">
                                <span class="text-sm {{ $notification->read_at ? 'text-ink-soft' : 'font-semibold text-ink' }}">{{ $notification->data['message'] ?? '' }}</span>
                                <span class="text-[13px] text-subtle">{{ $notification->created_at->format('j M Y, g:i A') }}</span>
                            </div>
                        </button>

                        @unless ($notification->read_at)
                            <flux:button variant="ghost" size="sm" wire:click="markAsRead('{{ $notification->id }}')">
                                {{ __('Mark as read') }}
                            </flux:button>
                        @endunless
                    </div>
                @endforeach
            </div>
        </x-panel>

        <div>
            {{ $this->notifications->links() }}
        </div>
    @endif
</div>
