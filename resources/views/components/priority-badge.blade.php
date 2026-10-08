@props([
    'priority',
])

@php
    $value = $priority instanceof \App\Enums\ApplicationPriority ? $priority->value : $priority;

    [$icon, $classes, $label] = match ($value) {
        'low' => ['arrow-down', 'border border-zinc-300 bg-white text-zinc-600', 'Low'],
        'normal' => ['minus', 'border border-blue-300 bg-white text-blue-600', 'Normal'],
        'high' => ['arrow-up', 'border border-transparent bg-amber-100 text-amber-800', 'High'],
        'urgent' => ['exclamation-triangle', 'border border-transparent bg-red-600 text-white', 'Urgent'],
        default => ['minus', 'border border-zinc-300 bg-white text-zinc-600', \Illuminate\Support\Str::headline($value)],
    };
@endphp

<span {{ $attributes->class(['inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium', $classes]) }}>
    <flux:icon :icon="$icon" variant="micro" class="size-3.5" />
    {{ $label }}
</span>
