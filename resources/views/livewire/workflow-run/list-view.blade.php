<x-layouts.window>
    <x-slot
        name="header"
        class="justify-self-end"
    >
        <h1 class="sr-only">Workflow Monitor</h1>

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
            <livewire:workflow-run.list-item
                :$run
                :wire:key="$run->id"
            />
        @endforeach
    </div>

    @if($this->runs->isEmpty())
        <x-support.no-runs />
    @endif

</x-layouts.window>
