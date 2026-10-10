<div class="flex flex-col gap-6 py-8">
    <x-page-header :title="$application->subject" :subtitle="$application->application_no">
        <x-slot:actions>
            <x-status-badge :status="$application->status" />
        </x-slot:actions>
    </x-page-header>

    <div class="flex flex-wrap items-start gap-6">
        <div class="flex min-w-0 flex-[999_1_640px] flex-col gap-6">
            <x-card>
                <dl class="m-0 flex flex-wrap gap-x-10 gap-y-4 pb-5">
                    <div class="flex flex-col gap-1">
                        <dt class="text-xs font-semibold tracking-[0.06em] text-subtle uppercase">{{ __('Category') }}</dt>
                        <dd class="m-0 text-[15px] font-semibold">{{ $application->category->name }}</dd>
                    </div>
                    <div class="flex flex-col gap-1">
                        <dt class="text-xs font-semibold tracking-[0.06em] text-subtle uppercase">{{ __('Submitted on') }}</dt>
                        <dd class="m-0 text-[15px] font-semibold">{{ $application->created_at->format('j F Y, g:i A') }}</dd>
                    </div>
                    <div class="flex flex-col gap-1">
                        <dt class="text-xs font-semibold tracking-[0.06em] text-subtle uppercase">{{ __('Semester at submission') }}</dt>
                        <dd class="m-0 text-[15px] font-semibold">{{ $application->semester_at_submission }}</dd>
                    </div>
                </dl>

                <flux:separator />

                <p class="m-0 pt-5 text-[15px] leading-relaxed whitespace-pre-line text-ink-soft">{{ $application->body }}</p>

                @if ($application->status === \App\Enums\ApplicationStatus::Resolved && $application->resolution_note)
                    <div class="mt-5 rounded-[10px] border border-[#CBE6D3] bg-[#E2F3E8] px-4 py-3.5">
                        <span class="block text-xs font-semibold tracking-[0.06em] text-[#1F6A3A] uppercase">{{ __('Resolution note') }}</span>
                        <p class="m-0 mt-1 text-sm leading-relaxed whitespace-pre-line text-[#1F6A3A]">{{ $application->resolution_note }}</p>
                    </div>
                @endif

                @if ($application->status === \App\Enums\ApplicationStatus::Rejected && $application->rejection_reason)
                    <div class="mt-5 rounded-[10px] border border-[#F3C4C0] bg-[#FCEBEA] px-4 py-3.5">
                        <span class="block text-xs font-semibold tracking-[0.06em] text-[#8A1C14] uppercase">{{ __('Rejection reason') }}</span>
                        <p class="m-0 mt-1 text-sm leading-relaxed whitespace-pre-line text-[#8A1C14]">{{ $application->rejection_reason }}</p>
                    </div>
                @endif
            </x-card>

            @if ($application->attachments->isNotEmpty())
                <x-panel :title="__('Attachments')">
                    <ul class="m-0 flex flex-col">
                        @foreach ($application->attachments as $attachment)
                            <li class="flex items-center justify-between gap-3 border-t border-border-soft px-[22px] py-3.5 first:border-t-0">
                                <span class="flex items-center gap-2.5 truncate text-sm text-ink-soft">
                                    <flux:icon icon="paper-clip" variant="micro" class="shrink-0 text-subtle" />
                                    {{ $attachment->original_name }}
                                </span>
                                <flux:button :href="route('applications.attachments.show', $attachment)" variant="ghost" size="sm" icon="arrow-down-tray">
                                    {{ __('Download') }}
                                </flux:button>
                            </li>
                        @endforeach
                    </ul>
                </x-panel>
            @endif

            <x-panel :title="__('Messages')">
                <div class="px-[22px] py-6 text-center text-sm text-subtle">
                    {{ __('Messaging with the Admin Office is not available yet.') }}
                </div>
            </x-panel>
        </div>

        <aside class="flex min-w-0 flex-1 basis-80 flex-col gap-6">
            <x-panel :title="__('Timeline')">
                @if ($application->events->isEmpty())
                    <div class="px-[22px] py-6 text-center text-sm text-subtle">{{ __('No activity yet.') }}</div>
                @else
                    <div class="flex flex-col">
                        @foreach ($application->events as $event)
                            <div class="flex gap-3 border-t border-border-soft px-[22px] py-3.5 first:border-t-0">
                                <span class="mt-1.5 size-2 shrink-0 rounded-full bg-plum"></span>
                                <div class="flex flex-col gap-0.5">
                                    <span class="text-sm leading-relaxed text-ink-soft">{{ $event->timelineDescription() }}</span>
                                    <span class="text-[13px] text-subtle">{{ $event->created_at->format('j M Y, g:i A') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-panel>
        </aside>
    </div>
</div>
