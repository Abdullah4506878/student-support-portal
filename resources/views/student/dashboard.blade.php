<x-layouts::student :title="__('Student Dashboard')">
    <x-page-header :title="__('Student Dashboard')" :subtitle="__('Your applications and announcements at a glance.')" />

    <div class="grid gap-4 sm:grid-cols-3">
        <x-stat-tile icon="clipboard-document-list" :label="__('Total Applications')" value="4" />
        <x-stat-tile icon="clock" :label="__('Awaiting Response')" value="1" />
        <x-stat-tile icon="check-circle" :label="__('Resolved')" value="2" />
    </div>

    <x-card class="mt-6">
        <flux:heading size="lg">{{ __('Recent Applications') }}</flux:heading>
        <flux:subheading>{{ __('A sample of how your applications will be listed.') }}</flux:subheading>

        <div class="mt-4 space-y-3">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-zinc-100 pb-3">
                <div>
                    <div class="font-medium text-zinc-800">SC-2026-000124</div>
                    <div class="text-sm text-zinc-600">{{ __('Fee Issue') }}</div>
                </div>
                <div class="flex items-center gap-2">
                    <x-priority-badge priority="normal" />
                    <x-status-badge status="under_review" />
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-zinc-100 pb-3">
                <div>
                    <div class="font-medium text-zinc-800">SC-2026-000119</div>
                    <div class="text-sm text-zinc-600">{{ __('Examination Issue') }}</div>
                </div>
                <div class="flex items-center gap-2">
                    <x-priority-badge priority="urgent" />
                    <x-status-badge status="info_required" />
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <div class="font-medium text-zinc-800">SC-2026-000091</div>
                    <div class="text-sm text-zinc-600">{{ __('Registration Issue') }}</div>
                </div>
                <div class="flex items-center gap-2">
                    <x-priority-badge priority="low" />
                    <x-status-badge status="resolved" />
                </div>
            </div>
        </div>
    </x-card>
</x-layouts::student>
