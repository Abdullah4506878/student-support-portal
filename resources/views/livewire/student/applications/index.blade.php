<div class="flex flex-col gap-6 py-8">
    <x-page-header :title="__('My applications')" :subtitle="__('Every application you have submitted, and where it stands.')">
        <x-slot:actions>
            <flux:button :href="route('student.applications.create')" variant="primary" icon="plus" wire:navigate>
                {{ __('New application') }}
            </flux:button>
        </x-slot:actions>
    </x-page-header>

    <div class="flex flex-wrap items-end gap-4 rounded-[14px] border border-border bg-white px-6 py-5">
        <div class="flex min-w-0 flex-1 basis-64 flex-col gap-1">
            <flux:input wire:model.live.debounce.400ms="search" :label="__('Search')" icon="magnifying-glass" :placeholder="__('Application number or subject')" />
        </div>

        <div class="flex min-w-0 flex-1 basis-48 flex-col gap-1">
            <flux:select wire:model.live="status" :label="__('Status')">
                <flux:select.option value="">{{ __('All statuses') }}</flux:select.option>
                @foreach ($this->statuses as $value => $label)
                    <flux:select.option :value="$value">{{ $label }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>

        @if ($this->hasActiveFilters)
            <flux:button variant="ghost" wire:click="clearFilters">{{ __('Clear filters') }}</flux:button>
        @endif
    </div>

    @if ($this->applications->isEmpty())
        <x-empty-state icon="inbox" :title="__('No applications found')" :description="__('Try a different search or filter, or submit a new application.')" />
    @else
        <x-panel :title="__('Applications')">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[640px] border-collapse text-sm">
                    <thead>
                        <tr class="text-left text-xs tracking-[0.06em] text-subtle uppercase">
                            <th class="px-[22px] py-3 font-semibold">{{ __('Application') }}</th>
                            <th class="px-3 py-3 font-semibold">{{ __('Category') }}</th>
                            <th class="px-3 py-3 font-semibold">{{ __('Submitted') }}</th>
                            <th class="px-[22px] py-3 font-semibold">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($this->applications as $application)
                            <tr class="border-t border-border-soft">
                                <td class="px-[22px] py-3.5">
                                    <a href="{{ route('student.applications.show', $application) }}" wire:navigate class="flex flex-col gap-0.5 no-underline">
                                        <span class="font-semibold text-ink">{{ $application->subject }}</span>
                                        <span class="text-[13px] text-subtle tabular-nums">{{ $application->application_no }}</span>
                                    </a>
                                </td>
                                <td class="px-3 py-3.5 text-ink-soft">{{ $application->category->name }}</td>
                                <td class="px-3 py-3.5 whitespace-nowrap text-ink-soft">{{ $application->created_at->format('j M, g:i A') }}</td>
                                <td class="px-[22px] py-3.5"><x-status-badge :status="$application->status" /></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-panel>

        <div>
            {{ $this->applications->links() }}
        </div>
    @endif
</div>
