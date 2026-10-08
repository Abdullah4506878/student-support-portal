@props([
    'title',
    'subtitle' => null,
])

<div {{ $attributes->class(['mb-2 flex flex-wrap items-end justify-between gap-4']) }}>
    <div class="flex flex-col gap-1.5">
        <h1 class="m-0 text-[28px] font-bold tracking-tight text-ink">{{ $title }}</h1>

        @if ($subtitle)
            <p class="m-0 text-[15px] text-muted">{{ $subtitle }}</p>
        @endif
    </div>

    @isset($actions)
        <div class="flex items-center gap-3">
            {{ $actions }}
        </div>
    @endisset
</div>
