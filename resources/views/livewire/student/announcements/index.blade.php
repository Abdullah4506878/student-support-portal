<div class="flex flex-col gap-6 py-8">
    <x-page-header :title="__('Announcements')" :subtitle="__('Updates from the SE Admin Office.')" />

    @if ($this->announcements->isEmpty())
        <x-empty-state icon="megaphone" :title="__('No announcements right now')" :description="__('Check back later for updates from the Admin Office.')" />
    @else
        <div class="flex flex-col gap-5">
            @foreach ($this->announcements as $announcement)
                <x-card>
                    <div class="flex flex-col gap-3">
                        <div class="flex flex-wrap items-start justify-between gap-2">
                            <h2 class="m-0 text-[17px] font-bold text-ink">{{ $announcement->title }}</h2>
                            <div class="flex items-center gap-2">
                                @if ($announcement->isRecentlyPublished())
                                    <flux:badge color="amber">{{ __('New') }}</flux:badge>
                                @endif
                                <span class="text-[13px] text-subtle whitespace-nowrap">
                                    {{ ($announcement->publish_at ?? $announcement->created_at)->format('j M Y') }}
                                </span>
                            </div>
                        </div>

                        @if ($announcement->image_path)
                            <img src="{{ route('announcements.image.show', $announcement) }}" alt="" class="w-full max-w-md rounded-[10px] border border-border-soft">
                        @endif

                        <p class="m-0 text-[15px] leading-relaxed whitespace-pre-line text-ink-soft">{{ $announcement->body }}</p>

                        @if ($announcement->attachment_path)
                            <flux:button :href="route('announcements.attachment.show', $announcement)" variant="ghost" size="sm" icon="paper-clip" class="self-start">
                                {{ $announcement->attachment_original_name ?? __('Download attachment') }}
                            </flux:button>
                        @endif
                    </div>
                </x-card>
            @endforeach
        </div>

        <div>
            {{ $this->announcements->links() }}
        </div>
    @endif
</div>
