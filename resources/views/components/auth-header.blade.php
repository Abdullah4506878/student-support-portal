@props([
    'title',
    'description' => null,
])

<div class="flex w-full flex-col gap-2">
    <h2 class="m-0 text-[28px] font-bold tracking-tight text-ink">{{ $title }}</h2>

    @if ($description)
        <p class="m-0 text-[15px] text-muted">{{ $description }}</p>
    @endif
</div>
