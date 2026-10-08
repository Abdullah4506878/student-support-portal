@props([
    'status',
])

@php
    $value = $status instanceof \App\Enums\ApplicationStatus ? $status->value : $status;

    [$icon, $classes, $label] = match ($value) {
        'submitted' => ['inbox-arrow-down', 'bg-[#EEECEE] text-[#3F3A3E]', 'Submitted'],
        'under_review' => ['magnifying-glass', 'bg-[#E3EEFB] text-[#1D4F8A]', 'Under Review'],
        'info_required' => ['exclamation-circle', 'bg-[#FFF1D6] text-[#7A4A00]', 'Info Required'],
        'in_progress' => ['arrow-path', 'bg-[#EFE7F7] text-[#5B3A86]', 'In Progress'],
        'resolved' => ['check-circle', 'bg-[#E2F3E8] text-[#1F6A3A]', 'Resolved'],
        'closed' => ['lock-closed', 'bg-[#EEECEE] text-[#3F3A3E]', 'Closed'],
        'rejected' => ['x-circle', 'bg-[#FCEBEA] text-[#8A1C14]', 'Rejected'],
        default => ['question-mark-circle', 'bg-[#EEECEE] text-[#3F3A3E]', \Illuminate\Support\Str::headline($value)],
    };
@endphp

<span {{ $attributes->class(['inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-2.5 py-1 text-[13px] font-semibold', $classes]) }}>
    <flux:icon :icon="$icon" variant="micro" class="size-3.5" />
    {{ $label }}
</span>
