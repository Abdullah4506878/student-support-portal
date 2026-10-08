@props([
    'status',
])

@php
    $value = $status instanceof \App\Enums\ApplicationStatus ? $status->value : $status;

    [$icon, $classes, $label] = match ($value) {
        'submitted' => ['inbox-arrow-down', 'bg-zinc-100 text-zinc-700', 'Submitted'],
        'under_review' => ['magnifying-glass', 'bg-blue-100 text-blue-700', 'Under Review'],
        'info_required' => ['exclamation-circle', 'bg-amber-100 text-amber-700', 'Info Required'],
        'in_progress' => ['arrow-path', 'bg-purple-100 text-purple-700', 'In Progress'],
        'resolved' => ['check-circle', 'bg-green-100 text-green-700', 'Resolved'],
        'closed' => ['lock-closed', 'bg-zinc-700 text-white', 'Closed'],
        'rejected' => ['x-circle', 'bg-red-100 text-red-700', 'Rejected'],
        default => ['question-mark-circle', 'bg-zinc-100 text-zinc-700', \Illuminate\Support\Str::headline($value)],
    };
@endphp

<span {{ $attributes->class(['inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium', $classes]) }}>
    <flux:icon :icon="$icon" variant="micro" class="size-3.5" />
    {{ $label }}
</span>
