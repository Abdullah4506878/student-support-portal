{{--
    Hides an already-shown Flux field error while the user is actively
    typing/editing it, without touching the error itself — Livewire's own
    round-trip (wire:model.live) clears the error server-side once the
    value is valid; this just keeps it visually hidden until then or blur.
--}}
<div
    x-data="{ editing: false }"
    x-on:focus.capture="editing = true"
    x-on:blur.capture="editing = false"
    x-bind:class="editing ? '[&_[data-flux-error]]:hidden' : ''"
>
    {{ $slot }}
</div>
