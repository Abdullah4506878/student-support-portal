<x-layouts::admin :title="__('Super Admin Dashboard')">
    <x-page-header :title="__('Super Admin Dashboard')" :subtitle="__('System-wide administration at a glance.')">
        <x-slot:actions>
            <x-confirm-modal
                name="demo-suspend"
                heading="{{ __('Suspend this student?') }}"
                text="{{ __('They will no longer be able to log in until reactivated.') }}"
                confirm-label="{{ __('Suspend') }}"
                variant="danger"
                confirm-action="suspendStudent(1)"
            >
                <x-slot:trigger>
                    <flux:button variant="danger" icon="user-minus">{{ __('Suspend Student (demo)') }}</flux:button>
                </x-slot:trigger>
            </x-confirm-modal>
        </x-slot:actions>
    </x-page-header>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-stat-tile icon="building-office-2" :label="__('Departments')" value="1" />
        <x-stat-tile icon="user-group" :label="__('Admin Officers')" value="1" />
        <x-stat-tile icon="users" :label="__('Students')" value="128" />
        <x-stat-tile icon="clipboard-document-list" :label="__('All Applications')" value="47" />
    </div>

    <x-card class="mt-6">
        <flux:heading size="lg">{{ __('Recent Activity') }}</flux:heading>
        <flux:subheading>{{ __('A sample of the audit log entries Super Admin can review.') }}</flux:subheading>

        <div class="mt-4 space-y-3 text-sm">
            <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                <span class="text-zinc-700">{{ __('Admin Officer updated application status') }}</span>
                <x-status-badge status="resolved" />
            </div>
            <div class="flex items-center justify-between">
                <span class="text-zinc-700">{{ __('Student submitted a new application') }}</span>
                <x-priority-badge priority="high" />
            </div>
        </div>
    </x-card>
</x-layouts::admin>
