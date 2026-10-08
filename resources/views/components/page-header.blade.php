@props([
    'title',
    'subtitle' => null,
])

<div {{ $attributes->class(['mb-6 flex flex-wrap items-start justify-between gap-4']) }}>
    <div>
        <flux:heading size="xl" level="1">{{ $title }}</flux:heading>

        @if ($subtitle)
            <flux:subheading size="lg">{{ $subtitle }}</flux:subheading>
        @endif
    </div>

    @isset($actions)
        <div class="flex items-center gap-3">
            {{ $actions }}
        </div>
    @endisset
</div>
