<div class="flex flex-col gap-6 py-7">
    <x-page-header :title="$student->user->name" :subtitle="$student->registration_no">
        <x-slot:actions>
            <flux:badge :color="$this->isSuspended ? 'red' : 'green'">
                {{ $this->isSuspended ? __('Suspended') : __('Active') }}
            </flux:badge>
        </x-slot:actions>
    </x-page-header>

    <div class="flex flex-wrap items-start gap-6">
        <div class="flex min-w-0 flex-[999_1_640px] flex-col gap-6">
            <x-card>
                <dl class="m-0 flex flex-wrap gap-x-10 gap-y-4">
                    <div class="flex flex-col gap-1">
                        <dt class="text-xs font-semibold tracking-[0.06em] text-subtle uppercase">{{ __('Program') }}</dt>
                        <dd class="m-0 text-[15px] font-semibold">{{ $student->program }}</dd>
                    </div>
                    <div class="flex flex-col gap-1">
                        <dt class="text-xs font-semibold tracking-[0.06em] text-subtle uppercase">{{ __('Batch') }}</dt>
                        <dd class="m-0 text-[15px] font-semibold">{{ $student->batch_label }}</dd>
                    </div>
                    <div class="flex flex-col gap-1">
                        <dt class="text-xs font-semibold tracking-[0.06em] text-subtle uppercase">{{ __('Current semester') }}</dt>
                        <dd class="m-0 text-[15px] font-semibold">{{ $student->current_semester }}</dd>
                    </div>
                    <div class="flex flex-col gap-1">
                        <dt class="text-xs font-semibold tracking-[0.06em] text-subtle uppercase">{{ __('Email') }}</dt>
                        <dd class="m-0 text-[15px] font-semibold">{{ $student->user->email }}</dd>
                    </div>
                </dl>
            </x-card>

            <x-panel :title="__('Applications')">
                @if ($this->applications->isEmpty())
                    <div class="px-[22px] py-6 text-center text-sm text-subtle">{{ __('No applications yet.') }}</div>
                @else
                    <ul class="m-0 flex flex-col">
                        @foreach ($this->applications as $application)
                            <li class="flex items-center justify-between gap-3 border-t border-border-soft px-[22px] py-3.5 first:border-t-0">
                                <a href="{{ route('admin.applications.show', $application) }}" wire:navigate class="flex flex-col gap-0.5 no-underline">
                                    <span class="font-semibold text-ink">{{ $application->subject }}</span>
                                    <span class="text-[13px] text-subtle tabular-nums">{{ $application->application_no }} &middot; {{ $application->category->name }} &middot; {{ $application->created_at->format('j M Y') }}</span>
                                </a>
                                <x-status-badge :status="$application->status" />
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-panel>
        </div>

        <aside class="flex min-w-0 flex-1 basis-80 flex-col gap-6">
            <x-panel :title="__('Correct name')">
                <div class="flex flex-col gap-3 px-[22px] py-4">
                    <flux:input wire:model="name" :label="__('Full name')" />
                    <flux:button variant="primary" class="self-end" wire:click="updateName">{{ __('Save') }}</flux:button>
                </div>
            </x-panel>

            <x-panel :title="__('Account')">
                <div class="flex flex-col gap-3 px-[22px] py-4">
                    @if ($this->isSuspended)
                        <x-confirm-modal
                            name="confirm-reactivate-student"
                            :heading="__('Reactivate this student?')"
                            :text="__('They will be able to log in again.')"
                            :confirmLabel="__('Reactivate')"
                            variant="primary"
                            confirmAction="reactivate"
                        >
                            <x-slot:trigger>
                                <flux:button variant="primary" class="w-full">{{ __('Reactivate student') }}</flux:button>
                            </x-slot:trigger>
                        </x-confirm-modal>
                    @else
                        <x-confirm-modal
                            name="confirm-suspend-student"
                            :heading="__('Suspend this student?')"
                            :text="__('They will be logged out immediately and unable to log in until reactivated.')"
                            :confirmLabel="__('Suspend')"
                            variant="danger"
                            confirmAction="suspend"
                        >
                            <x-slot:trigger>
                                <flux:button variant="danger" class="w-full">{{ __('Suspend student') }}</flux:button>
                            </x-slot:trigger>
                        </x-confirm-modal>
                    @endif
                </div>
            </x-panel>
        </aside>
    </div>
</div>
