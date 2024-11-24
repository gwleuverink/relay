<x-layouts.menu-panel>
    <x-slot
        name="header"
        class="justify-self-end"
    >
        <a
            wire:navigate.hover
            href="{{ route('settings') }}"
            class="cursor-default px-2 text-neutral-400 transition-colors hover:text-neutral-500"
        >
            <x-heroicon-c-cog-6-tooth class="w-3.5" />
        </a>
    </x-slot>

    <div
        x-init="autoAnimate($el)"
        class="divide-y divide-neutral-200 shadow-md"
    >
        @foreach ($this->runs as $run)
            <livewire:workflow-run
                :$run
                :wire:key="$run->id"
            />
        @endforeach
    </div>
</x-layouts.menu-panel>
