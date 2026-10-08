@props([
    'label',
    'value',
    'note' => null,
    'urgent' => false,
    'href' => null,
])

@php
    $tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }}
    @if ($href) href="{{ $href }}" @endif
    {{ $attributes->class([
        'flex flex-col gap-2 rounded-[14px] p-[18px] no-underline',
        'border border-border bg-white' => ! $urgent,
        'border border-[#F3C4C0] bg-[#FCEBEA]' => $urgent,
    ]) }}
>
    <span class="text-sm font-medium {{ $urgent ? 'text-[#8A1C14]' : 'text-muted' }}">{{ $label }}</span>
    <span class="text-[30px] font-bold tracking-tight tabular-nums {{ $urgent ? 'text-[#8A1C14]' : 'text-ink' }}">{{ $value }}</span>

    @if ($note)
        <span class="text-[13px] {{ $urgent ? 'text-[#8A1C14]' : 'text-subtle' }}">{{ $note }}</span>
    @endif
</{{ $tag }}>
