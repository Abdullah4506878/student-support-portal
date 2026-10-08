@props([
    'icon' => 'inbox',
    'title',
    'description' => null,
])

<div {{ $attributes->class(['flex flex-col items-center justify-center gap-3 rounded-[14px] border border-dashed border-border-input bg-white px-6 py-12 text-center']) }}>
    <flux:icon :icon="$icon" class="size-10 text-subtle" />

    <flux:heading size="lg">{{ $title }}</flux:heading>

    @if ($description)
        <flux:subheading>{{ $description }}</flux:subheading>
    @endif

    @isset($action)
        <div class="mt-2">
            {{ $action }}
        </div>
    @endisset
</div>
