@props([
    'class' => '',
    'white' => false,
])

@php
    $file = $white ? 'superior-logo-white.svg' : 'superior-logo.svg';
@endphp

@if (file_exists(public_path('images/'.$file)))
    <img
        src="{{ asset('images/'.$file) }}"
        alt="{{ config('app.name') }}"
        {{ $attributes->class(array_filter(['h-9 w-auto', $class])) }}
    />
@else
    <span {{ $attributes->class(['inline-flex flex-col leading-tight font-semibold', $white ? 'text-white' : 'text-ink', $class]) }}>
        <span class="font-display text-sm tracking-wide">Superior University</span>
        <span class="text-xs font-normal {{ $white ? 'text-white/80' : 'text-subtle' }}">SE Student Support</span>
    </span>
@endif
