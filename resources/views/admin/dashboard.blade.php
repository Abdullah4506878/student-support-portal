<x-layouts::admin :title="__('Admin Dashboard')">
    <x-page-header :title="__('Admin Officer Dashboard')" :subtitle="__('Your department\'s applications at a glance.')" />

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-stat-tile icon="inbox-arrow-down" :label="__('Submitted')" value="6" />
        <x-stat-tile icon="exclamation-circle" :label="__('Info Required')" value="2" />
        <x-stat-tile icon="arrow-path" :label="__('In Progress')" value="3" />
        <x-stat-tile icon="check-circle" :label="__('Resolved')" value="11" />
    </div>

    <x-card class="mt-6">
        <flux:heading size="lg">{{ __('Status & Priority Reference') }}</flux:heading>
        <flux:subheading>{{ __('Every badge used across the portal.') }}</flux:subheading>

        <div class="mt-4 flex flex-wrap gap-2">
            <x-status-badge status="submitted" />
            <x-status-badge status="under_review" />
            <x-status-badge status="info_required" />
            <x-status-badge status="in_progress" />
            <x-status-badge status="resolved" />
            <x-status-badge status="closed" />
            <x-status-badge status="rejected" />
        </div>

        <div class="mt-3 flex flex-wrap gap-2">
            <x-priority-badge priority="low" />
            <x-priority-badge priority="normal" />
            <x-priority-badge priority="high" />
            <x-priority-badge priority="urgent" />
        </div>
    </x-card>

    <div class="mt-6">
        <x-empty-state
            icon="megaphone"
            :title="__('No announcements yet')"
            :description="__('Announcements you publish for your department will appear here.')"
        />
    </div>
</x-layouts::admin>
