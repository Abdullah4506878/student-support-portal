@php
    $firstName = explode(' ', auth()->user()->name)[0];
    $hour = now()->hour;
    $greeting = match (true) {
        $hour < 12 => __('Good morning'),
        $hour < 17 => __('Good afternoon'),
        default => __('Good evening'),
    };
@endphp

<x-layouts::student :title="__('Student Dashboard')">
    <div class="flex flex-col gap-6 py-8">
        <x-page-header :title="__(':greeting, :name', ['greeting' => $greeting, 'name' => $firstName])" :subtitle="__('Here is where your applications stand today.')">
            <x-slot:actions>
                <flux:button :href="Route::has('student.applications.create') ? route('student.applications.create') : '#'" variant="primary" icon="plus" wire:navigate>
                    {{ __('New application') }}
                </flux:button>
            </x-slot:actions>
        </x-page-header>

        <section aria-label="{{ __('Your profile') }}" class="flex flex-wrap items-center gap-x-10 gap-y-5 rounded-[14px] border border-border bg-white px-6 py-5">
            <div class="flex flex-1 basis-64 items-center gap-3.5">
                <flux:avatar size="lg" :name="auth()->user()->name" :initials="auth()->user()->initials()" class="bg-avatar! text-plum-link!" />
                <div class="flex flex-col gap-0.5">
                    <span class="text-[17px] font-bold text-ink">{{ auth()->user()->name }}</span>
                    <span class="text-sm text-muted">{{ auth()->user()->student?->program ?? 'BS Software Engineering' }}</span>
                </div>
            </div>

            <dl class="m-0 flex flex-wrap gap-x-10 gap-y-4">
                <div class="flex flex-col gap-1">
                    <dt class="text-xs font-semibold tracking-[0.06em] text-subtle uppercase">{{ __('Registration no.') }}</dt>
                    <dd class="m-0 text-[15px] font-semibold tabular-nums">{{ auth()->user()->student?->registration_no }}</dd>
                </div>
                <div class="flex flex-col gap-1">
                    <dt class="text-xs font-semibold tracking-[0.06em] text-subtle uppercase">{{ __('Semester') }}</dt>
                    <dd class="m-0 text-[15px] font-semibold">{{ auth()->user()->student?->current_semester }}</dd>
                </div>
                <div class="flex flex-col gap-1">
                    <dt class="text-xs font-semibold tracking-[0.06em] text-subtle uppercase">{{ __('Batch') }}</dt>
                    <dd class="m-0 text-[15px] font-semibold">{{ auth()->user()->student?->batch_label }}</dd>
                </div>
                <div class="flex flex-col gap-1">
                    <dt class="text-xs font-semibold tracking-[0.06em] text-subtle uppercase">{{ __('Email') }}</dt>
                    <dd class="m-0 text-[15px] font-semibold">{{ auth()->user()->email }}</dd>
                </div>
            </dl>
        </section>

        <section aria-label="{{ __('Action needed') }}" class="flex flex-wrap items-center gap-4 rounded-[14px] border border-[#F1D9A6] bg-[#FFF7E8] px-5.5 py-4.5">
            <span class="flex size-10 shrink-0 items-center justify-center rounded-[10px] bg-[#FBE7BF] text-[#8A5300]">
                <flux:icon icon="exclamation-circle" variant="outline" class="size-5" />
            </span>
            <div class="flex flex-1 basis-80 flex-col gap-0.5">
                <span class="text-[15px] font-bold text-[#5C3A00]">{{ __('The Admin Office needs a document from you') }}</span>
                <span class="text-sm text-[#6E4A10]">SC-2026-000119 &middot; {{ __('Examination issue — "Please upload your latest fee challan so we can proceed."') }}</span>
            </div>
            <flux:button href="#" variant="primary" class="bg-[#8A5300]! hover:bg-[#6E4210]!">
                {{ __('Upload document') }}
            </flux:button>
        </section>

        <section aria-label="{{ __('Summary') }}" class="grid grid-cols-[repeat(auto-fit,minmax(200px,1fr))] gap-4">
            <x-stat-tile :label="__('Total applications')" value="4" :note="__('Since Fall 2022')" />
            <x-stat-tile :label="__('Waiting on you')" value="1" :note="__('Document requested')" />
            <x-stat-tile :label="__('Being processed')" value="1" :note="__('Under review')" />
            <x-stat-tile :label="__('Resolved')" value="2" :note="__('Last one on 2 Oct')" />
        </section>

        <div class="flex flex-wrap items-start gap-6">
            <x-panel :title="__('My applications')" class="min-w-0 flex-[999_1_640px]">
                <x-slot:actions>
                    <flux:link href="#" class="text-sm font-semibold">{{ __('View all') }}</flux:link>
                </x-slot:actions>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[640px] border-collapse text-sm">
                        <thead>
                            <tr class="text-left text-xs tracking-[0.06em] text-subtle uppercase">
                                <th class="px-[22px] py-3 font-semibold">{{ __('Application') }}</th>
                                <th class="px-3 py-3 font-semibold">{{ __('Category') }}</th>
                                <th class="px-3 py-3 font-semibold">{{ __('Last update') }}</th>
                                <th class="px-[22px] py-3 font-semibold">{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ([
                                ['subject' => 'Exam result not updated on ERP', 'id' => 'SC-2026-000119', 'category' => 'Examination', 'updated' => '8 Oct, 11:30 AM', 'status' => 'info_required'],
                                ['subject' => 'Fee challan shows wrong amount', 'id' => 'SC-2026-000124', 'category' => 'Fee', 'updated' => '7 Oct, 4:10 PM', 'status' => 'under_review'],
                                ['subject' => 'Course registration for FYP-II', 'id' => 'SC-2026-000091', 'category' => 'Registration', 'updated' => '2 Oct, 10:05 AM', 'status' => 'resolved'],
                                ['subject' => 'LMS access after password reset', 'id' => 'SC-2026-000063', 'category' => 'LMS / Portal', 'updated' => '21 Sep, 9:40 AM', 'status' => 'resolved'],
                            ] as $app)
                                <tr class="border-t border-border-soft">
                                    <td class="px-[22px] py-3.5">
                                        <div class="flex flex-col gap-0.5">
                                            <a href="#" class="font-semibold text-ink no-underline">{{ $app['subject'] }}</a>
                                            <span class="text-[13px] text-subtle tabular-nums">{{ $app['id'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3.5 text-ink-soft">{{ $app['category'] }}</td>
                                    <td class="px-3 py-3.5 whitespace-nowrap text-ink-soft">{{ $app['updated'] }}</td>
                                    <td class="px-[22px] py-3.5"><x-status-badge :status="$app['status']" /></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-panel>

            <aside class="flex min-w-0 flex-1 basis-80 flex-col gap-6">
                <x-panel :title="__('Announcements')">
                    <x-slot:actions>
                        <flux:link href="#" class="text-sm font-semibold">{{ __('See all') }}</flux:link>
                    </x-slot:actions>

                    @foreach ([
                        ['tag' => 'Event', 'title' => 'IEEE Tech Talk 2026 — registration open', 'date' => 'Posted 6 Oct · closes 15 Oct'],
                        ['tag' => 'Deadline', 'title' => 'FYP-II proposal submission', 'date' => 'Posted 3 Oct · due 20 Oct'],
                        ['tag' => 'Notice', 'title' => 'Saturday classes rescheduled', 'date' => 'Posted 1 Oct'],
                    ] as $announcement)
                        <a href="#" class="flex flex-col gap-1 border-t border-border-soft px-[22px] py-4 no-underline first:border-t-0">
                            <span class="text-xs font-semibold tracking-[0.06em] text-plum-link uppercase">{{ $announcement['tag'] }}</span>
                            <span class="text-[15px] font-semibold text-ink">{{ $announcement['title'] }}</span>
                            <span class="text-[13px] text-subtle">{{ $announcement['date'] }}</span>
                        </a>
                    @endforeach
                </x-panel>

                <x-panel :title="__('Latest updates')">
                    @foreach ([
                        ['text' => 'Admin Office requested a document on SC-2026-000119', 'time' => 'Today, 11:30 AM', 'dot' => '#C27A00'],
                        ['text' => 'SC-2026-000124 moved to Under review', 'time' => 'Yesterday, 4:10 PM', 'dot' => '#2F6DB3'],
                        ['text' => 'SC-2026-000091 was marked Resolved', 'time' => '2 Oct, 10:05 AM', 'dot' => '#2E8A4E'],
                    ] as $update)
                        <div class="flex gap-3 border-t border-border-soft px-[22px] py-3.5 first:border-t-0">
                            <span class="mt-1.5 size-2 shrink-0 rounded-full" style="background: {{ $update['dot'] }}"></span>
                            <div class="flex flex-col gap-0.5">
                                <span class="text-sm leading-relaxed text-ink-soft">{{ $update['text'] }}</span>
                                <span class="text-[13px] text-subtle">{{ $update['time'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </x-panel>
            </aside>
        </div>
    </div>
</x-layouts::student>
