@props([
    'priority',
])

@php
    $value = $priority instanceof \App\Enums\ApplicationPriority ? $priority->value : $priority;

    [$icon, $classes, $label] = match ($value) {
        'low' => ['arrow-down', 'bg-white text-[#4F4850] ring-1 ring-inset ring-[#CFC8CD]', 'Low'],
        'normal' => ['minus', 'bg-white text-[#1D4F8A] ring-1 ring-inset ring-[#9DBCE3]', 'Normal'],
        'high' => ['arrow-up', 'bg-[#FFF1D6] text-[#7A4A00]', 'High'],
        'urgent' => ['exclamation-triangle', 'bg-[#B42318] text-white', 'Urgent'],
        default => ['minus', 'bg-white text-[#4F4850] ring-1 ring-inset ring-[#CFC8CD]', \Illuminate\Support\Str::headline($value)],
    };
@endphp

<span {{ $attributes->class(['inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-2.5 py-1 text-[13px] font-semibold', $classes]) }}>
    <flux:icon :icon="$icon" variant="micro" class="size-3.5" />
    {{ $label }}
</span>
