<x-layouts::admin :title="__('Super Admin Dashboard')">
    <div class="flex flex-col gap-6 py-7">
        <x-page-header :title="__('System overview')" :subtitle="__('All departments · :date', ['date' => now()->format('l, j F Y')])" />

        <section aria-label="{{ __('Summary') }}" class="grid grid-cols-[repeat(auto-fit,minmax(170px,1fr))] gap-3.5">
            <x-stat-tile :label="__('Departments')" value="1" :note="__('Software Engineering')" />
            <x-stat-tile :label="__('Admin Officers')" value="1" :note="__('Across all departments')" />
            <x-stat-tile :label="__('Students')" value="128" :note="__('Registered accounts')" />
            <x-stat-tile :label="__('All applications')" value="47" :note="__('All time')" />
        </section>

        <div class="flex flex-wrap items-start gap-6">
            <x-panel :title="__('Recent activity')" class="min-w-0 flex-[999_1_640px]">
                @foreach ([
                    ['text' => 'Admin Officer updated SC-2026-000124 status to Resolved', 'time' => 'Today, 9:40 AM', 'dot' => '#2E8A4E'],
                    ['text' => 'Student Ayesha Khan submitted a new application', 'time' => 'Today, 8:15 AM', 'dot' => '#875A7B'],
                    ['text' => 'Admin Officer changed SC-2026-000124 priority to Urgent', 'time' => 'Yesterday', 'dot' => '#B42318'],
                ] as $activity)
                    <div class="flex gap-3 border-t border-border-soft px-[22px] py-3.5 first:border-t-0">
                        <span class="mt-1.5 size-2 shrink-0 rounded-full" style="background: {{ $activity['dot'] }}"></span>
                        <div class="flex flex-col gap-0.5">
                            <span class="text-sm leading-relaxed text-ink-soft">{{ $activity['text'] }}</span>
                            <span class="text-[13px] text-subtle">{{ $activity['time'] }}</span>
                        </div>
                    </div>
                @endforeach
            </x-panel>

            <aside class="flex min-w-0 flex-1 basis-72 flex-col gap-6">
                <x-empty-state
                    icon="megaphone"
                    :title="__('No announcements yet')"
                    :description="__('Announcements from every department will appear here.')"
                />
            </aside>
        </div>
    </div>
</x-layouts::admin>
