@props([
    'padding' => true,
])

<div {{ $attributes->class(['rounded-[14px] border border-border bg-white', 'p-5' => $padding]) }}>
    {{ $slot }}
</div>
