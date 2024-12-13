@props(['repositories'])

<button
    {{-- This forces a re-render when the dom is diffed - otherwise the avalaible menu items won't be reflected --}}
    wire:key="{{ str()->random(5) }}"
    x-data="{
        canResetSelection: @js(! empty($repositories)),
    }"
    x-on:click="
        $contextMenu([
            ...(canResetSelection
                ? [
                      {
                          label: 'Reset selection',
                          click: async () => $wire.resetSelection(),
                      },
                      { type: 'separator' },
                  ]
                : []),
            {
                label: 'Clear caches',
                click: async () => $wire.clearCaches(),
            },
            {
                label: 'Delete runs',
                click: async () => $wire.clearRuns(),
            },
            { type: 'separator' },
            {
                label: 'Logout',
                click: async () => $wire.logout(),
            },
        ])
    "
    type="button"
    class="cursor-default rounded-full p-1 text-neutral-400 ring-indigo-200 transition-colors hover:text-neutral-500 focus:outline-none focus:ring-2"
>
    <x-heroicon-c-ellipsis-vertical class="w-3.5" />
</button>
