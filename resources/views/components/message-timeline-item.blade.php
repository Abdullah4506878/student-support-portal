@props([
    'message',
])

@php
    $isRequest = in_array($message->type, [\App\Enums\MessageType::InfoRequest, \App\Enums\MessageType::DocumentRequest], true);

    $label = match ($message->type) {
        \App\Enums\MessageType::Message => __('Message'),
        \App\Enums\MessageType::InfoRequest => __('Info requested'),
        \App\Enums\MessageType::DocumentRequest => __('Document requested'),
        \App\Enums\MessageType::StudentResponse => __('Response'),
    };

    $requestStatus = match (true) {
        ! $isRequest => null,
        (bool) $message->cancelled_at => ['label' => __('Cancelled'), 'color' => '#6B626B'],
        (bool) $message->responded_at => ['label' => __('Answered'), 'color' => '#1F6A3A'],
        default => ['label' => __('Waiting for response'), 'color' => '#7A4A00'],
    };
@endphp

<div class="flex gap-3 border-t border-border-soft px-[22px] py-3.5 first:border-t-0">
    <span class="mt-1.5 size-2 shrink-0 rounded-full bg-plum-link"></span>
    <div class="flex flex-col gap-1">
        <span class="text-[13px] font-semibold text-subtle">{{ $message->sender->name }} &middot; {{ $label }}</span>

        @if ($message->body !== '')
            <p class="m-0 text-sm leading-relaxed whitespace-pre-line text-ink-soft">{{ $message->body }}</p>
        @endif

        @if ($message->attachments->isNotEmpty())
            <div class="flex flex-col gap-1">
                @foreach ($message->attachments as $attachment)
                    <a href="{{ route('applications.attachments.show', $attachment) }}" class="inline-flex items-center gap-1.5 text-[13px] font-semibold text-plum-link no-underline">
                        <flux:icon icon="paper-clip" variant="micro" />
                        {{ $attachment->original_name }}
                    </a>
                @endforeach
            </div>
        @endif

        @if ($requestStatus)
            <span class="text-[12px] font-medium" style="color: {{ $requestStatus['color'] }}">{{ $requestStatus['label'] }}</span>
        @endif

        <span class="text-[13px] text-subtle">{{ $message->created_at->format('j M Y, g:i A') }}</span>
    </div>
</div>
