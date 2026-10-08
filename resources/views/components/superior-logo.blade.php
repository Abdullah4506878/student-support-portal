@props([
    'class' => '',
    'white' => false,
])

@if (file_exists(public_path('images/superior-logo.png')))
    <img
        src="{{ asset('images/superior-logo.png') }}"
        alt="{{ config('app.name') }}"
        {{ $attributes->class(array_filter(['h-9 w-auto', $white ? 'brightness-0 invert' : '', $class])) }}
    />
@else
    <span {{ $attributes->class(['inline-flex flex-col leading-tight font-semibold', $white ? 'text-white' : 'text-ink', $class]) }}>
        <span class="font-display text-sm tracking-wide">Superior University</span>
        <span class="text-xs font-normal {{ $white ? 'text-white/80' : 'text-subtle' }}">SE Student Support</span>
    </span>
@endif
