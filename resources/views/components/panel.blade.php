@props([
    'title',
])

<div {{ $attributes->class(['overflow-hidden rounded-[14px] border border-border bg-white']) }}>
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-border-soft px-[22px] py-[18px]">
        <h2 class="text-[17px] font-bold text-ink">{{ $title }}</h2>

        {{ $actions ?? '' }}
    </div>

    {{ $slot }}
</div>
