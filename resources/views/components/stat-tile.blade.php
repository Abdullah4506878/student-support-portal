@props([
    'label',
    'value',
    'icon' => null,
])

<div {{ $attributes->class(['flex items-center gap-4 rounded-lg bg-stat-purple p-5']) }}>
    @if ($icon)
        <flux:icon :icon="$icon" class="size-8 shrink-0 text-plum-dark" />
    @endif

    <div>
        <div class="text-2xl font-semibold text-zinc-800">{{ $value }}</div>
        <div class="text-sm text-zinc-600">{{ $label }}</div>
    </div>
</div>
