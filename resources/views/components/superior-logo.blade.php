@props([
    'class' => '',
])

@if (file_exists(public_path('images/superior-logo.png')))
    <img src="{{ asset('images/superior-logo.png') }}" alt="{{ config('app.name') }}" {{ $attributes->class(['h-8 w-auto', $class]) }} />
@else
    <span {{ $attributes->class(['inline-flex flex-col leading-tight font-semibold text-white', $class]) }}>
        <span class="text-sm tracking-wide">Superior University</span>
        <span class="text-xs font-normal text-white/80">SE Student Support</span>
    </span>
@endif
