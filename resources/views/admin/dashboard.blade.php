<x-layouts::admin :title="__('Admin Dashboard')">
    <div class="flex flex-col gap-6 py-7">
        <x-page-header :title="__('Applications overview')" :subtitle="__(':department &middot; :date', ['department' => auth()->user()->department?->name ?? __('No department assigned'), 'date' => now()->format('l, j F Y')])" />

        <section aria-label="{{ __('Queue summary') }}" class="grid grid-cols-[repeat(auto-fit,minmax(170px,1fr))] gap-3.5">
            <x-stat-tile :href="route('admin.applications.index', ['status' => 'submitted'])" :label="__('New')" :value="$stats['new']" />
            <x-stat-tile :href="route('admin.applications.index', ['status' => 'under_review'])" :label="__('Under review')" :value="$stats['under_review']" />
            <x-stat-tile :href="route('admin.applications.index', ['status' => 'info_required'])" :label="__('Waiting on student')" :value="$stats['waiting_on_student']" :note="__('Info or document')" />
            <x-stat-tile :href="route('admin.applications.index', ['status' => 'in_progress'])" :label="__('In progress')" :value="$stats['in_progress']" />
            <x-stat-tile :href="route('admin.applications.index', ['priority' => 'urgent'])" :label="__('Urgent')" :value="$stats['urgent']" :note="__('Handle first')" urgent />
        </section>

        <div class="flex flex-wrap items-start gap-6">
            <x-panel :title="__('Open applications')" class="min-w-0 flex-[999_1_640px]">
                <x-slot:actions>
                    <div role="group" aria-label="{{ __('Filter by status') }}" class="flex flex-wrap gap-1.5">
                        <flux:link :href="route('admin.applications.index')" class="min-h-9 rounded-full border border-plum bg-plum px-3.5 py-1.5 text-[13px] font-semibold text-white no-underline" wire:navigate>{{ __('All open') }}</flux:link>
                        <flux:link :href="route('admin.applications.index', ['status' => 'submitted'])" class="min-h-9 rounded-full border border-border-input bg-white px-3.5 py-1.5 text-[13px] font-semibold text-ink-soft no-underline" wire:navigate>{{ __('New') }}</flux:link>
                        <flux:link :href="route('admin.applications.index', ['priority' => 'urgent'])" class="min-h-9 rounded-full border border-border-input bg-white px-3.5 py-1.5 text-[13px] font-semibold text-ink-soft no-underline" wire:navigate>{{ __('Urgent') }}</flux:link>
                        <flux:link :href="route('admin.applications.index', ['status' => 'info_required'])" class="min-h-9 rounded-full border border-border-input bg-white px-3.5 py-1.5 text-[13px] font-semibold text-ink-soft no-underline" wire:navigate>{{ __('Waiting on student') }}</flux:link>
                    </div>
                </x-slot:actions>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[820px] border-collapse text-sm">
                        <thead>
                            <tr class="bg-[#FAF8F9] text-left text-xs tracking-[0.06em] text-subtle uppercase">
                                <th class="px-[22px] py-3 font-semibold">{{ __('Application') }}</th>
                                <th class="px-3 py-3 font-semibold">{{ __('Student') }}</th>
                                <th class="px-3 py-3 font-semibold">{{ __('Sem.') }}</th>
                                <th class="px-3 py-3 font-semibold">{{ __('Priority') }}</th>
                                <th class="px-3 py-3 font-semibold">{{ __('Status') }}</th>
                                <th class="px-3 py-3 font-semibold">{{ __('Updated') }}</th>
                                <th class="px-[22px] py-3 font-semibold"><span class="sr-only">{{ __('Action') }}</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($openApplications as $application)
                                @php
                                    $priorApplications = $application->student->applications()->count() - 1;
                                @endphp
                                <tr class="border-t border-border-soft">
                                    <td class="px-[22px] py-3.5">
                                        <div class="flex flex-col gap-0.5">
                                            <span class="font-semibold text-ink">{{ $application->subject }}</span>
                                            <span class="text-[13px] text-subtle tabular-nums">{{ $application->application_no }} &middot; {{ $application->category->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3.5">
                                        <div class="flex flex-col gap-0.5">
                                            <span class="font-medium text-ink">{{ $application->student->user->name }}</span>
                                            <span class="text-[13px] text-subtle tabular-nums">{{ $application->student->registration_no }} &middot; {{ $priorApplications > 0 ? __(':count earlier', ['count' => $priorApplications]) : __('First application') }}</span>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3.5 tabular-nums text-ink-soft">{{ $application->semester_at_submission }}</td>
                                    <td class="px-3 py-3.5"><x-priority-badge :priority="$application->priority" /></td>
                                    <td class="px-3 py-3.5"><x-status-badge :status="$application->status" /></td>
                                    <td class="px-3 py-3.5 whitespace-nowrap text-ink-soft">{{ $application->updated_at->diffForHumans() }}</td>
                                    <td class="px-[22px] py-3.5 text-right">
                                        <a href="{{ route('admin.applications.show', $application) }}" class="inline-flex min-h-9 items-center rounded-lg border border-border-input px-3.5 text-[13px] font-semibold text-plum-dark no-underline">{{ __('Open') }}</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-[22px] py-8 text-center text-sm text-subtle">{{ __('No open applications.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="flex items-center justify-between border-t border-border-soft px-[22px] py-3.5 text-[13px] text-subtle">
                    <span>{{ __('Showing :shown of :total open applications', ['shown' => $openApplications->count(), 'total' => $openTotal]) }}</span>
                    <flux:link :href="route('admin.applications.index')" class="font-semibold" wire:navigate>{{ __('View all applications') }}</flux:link>
                </div>
            </x-panel>

            <aside class="flex min-w-0 flex-1 basis-72 flex-col gap-6">
                <x-panel :title="__('Needs attention')">
                    @forelse ($needsAttention as $application)
                        @php
                            $hasNewResponse = $application->unreadStudentResponses->isNotEmpty();
                            $isUrgent = $application->priority->value === 'urgent';
                            $daysSinceUpdate = $application->updated_at->diffInDays(now());
                            $reasonColor = $hasNewResponse ? '#1D4F8A' : ($isUrgent ? '#8A1C14' : '#7A4A00');
                            $reason = match (true) {
                                $hasNewResponse => __('New response'),
                                $isUrgent => __('Urgent'),
                                default => __('No update for :days days', ['days' => $daysSinceUpdate]),
                            };
                        @endphp
                        <a href="{{ route('admin.applications.show', $application) }}" class="flex flex-col gap-1 border-t border-border-soft px-[22px] py-3.5 no-underline first:border-t-0">
                            <span class="text-sm font-semibold text-ink">{{ $application->application_no }} &middot; {{ $application->category->name }}</span>
                            <span class="text-[13px] font-medium" style="color: {{ $reasonColor }}">{{ $reason }}</span>
                        </a>
                    @empty
                        <div class="px-[22px] py-6 text-center text-sm text-subtle">{{ __('Nothing needs attention right now.') }}</div>
                    @endforelse
                </x-panel>

                <x-panel :title="__('Recent activity')">
                    @forelse ($recentActivity as $event)
                        <div class="flex gap-3 border-t border-border-soft px-[22px] py-3.5 first:border-t-0">
                            <span class="mt-1.5 size-2 shrink-0 rounded-full bg-plum"></span>
                            <div class="flex flex-col gap-0.5">
                                <span class="text-sm leading-relaxed text-ink-soft">{{ $event->description() }}</span>
                                <span class="text-[13px] text-subtle">{{ $event->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="px-[22px] py-6 text-center text-sm text-subtle">{{ __('No recent activity.') }}</div>
                    @endforelse
                </x-panel>
            </aside>
        </div>
    </div>
</x-layouts::admin>
