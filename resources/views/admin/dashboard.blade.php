<x-layouts::admin :title="__('Admin Dashboard')">
    <div class="flex flex-col gap-6 py-7">
        <x-page-header :title="__('Applications overview')" :subtitle="__('Software Engineering Department · :date', ['date' => now()->format('l, j F Y')])" />

        <section aria-label="{{ __('Queue summary') }}" class="grid grid-cols-[repeat(auto-fit,minmax(170px,1fr))] gap-3.5">
            <x-stat-tile href="#" :label="__('New')" value="6" :note="__('2 today')" />
            <x-stat-tile href="#" :label="__('Under review')" value="8" :note="__('Oldest: 4 days')" />
            <x-stat-tile href="#" :label="__('Waiting on student')" value="2" :note="__('Info or document')" />
            <x-stat-tile href="#" :label="__('In progress')" value="3" :note="__('With other offices')" />
            <x-stat-tile href="#" :label="__('Urgent')" value="3" :note="__('Handle first')" urgent />
        </section>

        <div class="flex flex-wrap items-start gap-6">
            <x-panel :title="__('Open applications')" class="min-w-0 flex-[999_1_640px]">
                <x-slot:actions>
                    <div role="group" aria-label="{{ __('Filter by status') }}" class="flex flex-wrap gap-1.5">
                        <button type="button" class="min-h-9 rounded-full border border-plum bg-plum px-3.5 text-[13px] font-semibold text-white">{{ __('All open') }}</button>
                        <button type="button" class="min-h-9 rounded-full border border-border-input bg-white px-3.5 text-[13px] font-semibold text-ink-soft">{{ __('New') }}</button>
                        <button type="button" class="min-h-9 rounded-full border border-border-input bg-white px-3.5 text-[13px] font-semibold text-ink-soft">{{ __('Urgent') }}</button>
                        <button type="button" class="min-h-9 rounded-full border border-border-input bg-white px-3.5 text-[13px] font-semibold text-ink-soft">{{ __('Waiting on student') }}</button>
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
                            @foreach ([
                                ['subject' => 'Fee challan shows wrong amount', 'id' => 'SC-2026-000124', 'category' => 'Fee', 'student' => 'Muhammad Ali', 'reg' => 'F22-118', 'history' => '3rd application', 'sem' => '5', 'priority' => 'urgent', 'status' => 'under_review', 'updated' => '25 min ago'],
                                ['subject' => 'Exam result not updated on ERP', 'id' => 'SC-2026-000119', 'category' => 'Examination', 'student' => 'Ayesha Khan', 'reg' => 'F24-042', 'history' => 'First application', 'sem' => '3', 'priority' => 'high', 'status' => 'info_required', 'updated' => '1 hr ago'],
                                ['subject' => 'Attendance marked absent wrongly', 'id' => 'SC-2026-000127', 'category' => 'Attendance', 'student' => 'Hamza Iqbal', 'reg' => 'F23-077', 'history' => '1 earlier', 'sem' => '7', 'priority' => 'normal', 'status' => 'submitted', 'updated' => 'Today, 9:12 AM'],
                                ['subject' => 'Scholarship form not accepted', 'id' => 'SC-2026-000126', 'category' => 'Scholarship', 'student' => 'Fatima Noor', 'reg' => 'F25-009', 'history' => 'First application', 'sem' => '1', 'priority' => 'normal', 'status' => 'in_progress', 'updated' => 'Yesterday'],
                                ['subject' => 'Timetable clash: SPM and IS', 'id' => 'SC-2026-000122', 'category' => 'Timetable', 'student' => 'Usman Tariq', 'reg' => 'F22-203', 'history' => '2 earlier', 'sem' => '8', 'priority' => 'high', 'status' => 'under_review', 'updated' => '6 Oct'],
                                ['subject' => 'Cannot log in to LMS', 'id' => 'SC-2026-000120', 'category' => 'LMS / Portal', 'student' => 'Sana Riaz', 'reg' => 'F24-150', 'history' => 'First application', 'sem' => '4', 'priority' => 'low', 'status' => 'submitted', 'updated' => '5 Oct'],
                            ] as $row)
                                <tr class="border-t border-border-soft">
                                    <td class="px-[22px] py-3.5">
                                        <div class="flex flex-col gap-0.5">
                                            <span class="font-semibold text-ink">{{ $row['subject'] }}</span>
                                            <span class="text-[13px] text-subtle tabular-nums">{{ $row['id'] }} &middot; {{ $row['category'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3.5">
                                        <div class="flex flex-col gap-0.5">
                                            <span class="font-medium text-ink">{{ $row['student'] }}</span>
                                            <span class="text-[13px] text-subtle tabular-nums">{{ $row['reg'] }} &middot; {{ $row['history'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3.5 tabular-nums text-ink-soft">{{ $row['sem'] }}</td>
                                    <td class="px-3 py-3.5"><x-priority-badge :priority="$row['priority']" /></td>
                                    <td class="px-3 py-3.5"><x-status-badge :status="$row['status']" /></td>
                                    <td class="px-3 py-3.5 whitespace-nowrap text-ink-soft">{{ $row['updated'] }}</td>
                                    <td class="px-[22px] py-3.5 text-right">
                                        <a href="#" class="inline-flex min-h-9 items-center rounded-lg border border-border-input px-3.5 text-[13px] font-semibold text-plum-dark no-underline">{{ __('Open') }}</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex items-center justify-between border-t border-border-soft px-[22px] py-3.5 text-[13px] text-subtle">
                    <span>{{ __('Showing 6 of 14 open applications') }}</span>
                    <flux:link href="#" class="font-semibold">{{ __('View all applications') }}</flux:link>
                </div>
            </x-panel>

            <aside class="flex min-w-0 flex-1 basis-72 flex-col gap-6">
                <x-panel :title="__('Needs attention')">
                    @foreach ([
                        ['title' => 'SC-2026-000124 · Fee challan', 'reason' => 'Urgent · student has 2 earlier fee issues', 'color' => '#8A1C14'],
                        ['title' => 'SC-2026-000122 · Timetable clash', 'reason' => 'Under review for 4 days', 'color' => '#7A4A00'],
                        ['title' => 'SC-2026-000119 · Exam result', 'reason' => 'Student uploaded the requested document', 'color' => '#1D4F8A'],
                    ] as $item)
                        <a href="#" class="flex flex-col gap-1 border-t border-border-soft px-[22px] py-3.5 no-underline first:border-t-0">
                            <span class="text-sm font-semibold text-ink">{{ $item['title'] }}</span>
                            <span class="text-[13px] font-medium" style="color: {{ $item['color'] }}">{{ $item['reason'] }}</span>
                        </a>
                    @endforeach
                </x-panel>

                <x-panel :title="__('Recent activity')">
                    @foreach ([
                        ['text' => 'Ayesha Khan uploaded a fee challan to SC-2026-000119', 'time' => '10 min ago', 'dot' => '#2F6DB3'],
                        ['text' => 'New application SC-2026-000127 from Hamza Iqbal', 'time' => 'Today, 9:12 AM', 'dot' => '#875A7B'],
                        ['text' => 'You changed SC-2026-000124 priority to Urgent', 'time' => 'Today, 8:40 AM', 'dot' => '#B42318'],
                        ['text' => 'You marked SC-2026-000091 as Resolved', 'time' => 'Yesterday', 'dot' => '#2E8A4E'],
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
            </aside>
        </div>
    </div>
</x-layouts::admin>
