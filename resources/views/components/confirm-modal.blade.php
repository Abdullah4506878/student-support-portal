@props([
    'name',
    'heading',
    'text' => null,
    'confirmLabel' => 'Confirm',
    'cancelLabel' => 'Cancel',
    'variant' => 'danger',
    'confirmAction',
])

<flux:modal.trigger name="{{ $name }}">
    {{ $trigger }}
</flux:modal.trigger>

<flux:modal name="{{ $name }}" class="md:w-96">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">{{ $heading }}</flux:heading>

            @if ($text)
                <flux:subheading>{{ $text }}</flux:subheading>
            @endif
        </div>

        <div class="flex gap-2">
            <flux:spacer />

            <flux:modal.close>
                <flux:button variant="ghost">{{ $cancelLabel }}</flux:button>
            </flux:modal.close>

            <flux:button :variant="$variant" wire:click="{{ $confirmAction }}">
                {{ $confirmLabel }}
            </flux:button>
        </div>
    </div>
</flux:modal>
