@props(['run'])

<button
    x-data="{
        canRestart: @js($run->canRestart()),
        canCancel: @js($run->canCancel()),
    }"
    x-on:click="
        $contextMenu([
            ...(canCancel
                ? [
                      {
                          label: 'Cancel Run',
                          click: async () => $wire.cancelRun(),
                      },
                      { type: 'separator' },
                  ]
                : []),
            ...(canRestart
                ? [
                      {
                          label: 'Re-run all jobs',
                          click: async () => $wire.restartJobs(),
                      },
                      {
                          label: 'Re-run failed jobs',
                          click: async () => $wire.restartFailedJobs(),
                      },
                      { type: 'separator' },
                  ]
                : []),
            {
                label: 'Inspect',
                click: async () => $wire.viewRun(),
            },
            {
                label: 'Open in GitHub',
                click: async () => shell.openExternal('{{ $run->data->html_url }}'),
            },
        ])
    "
    type="button"
    class="group/context cursor-default opacity-20 transition-opacity group-hover:opacity-100"
>
    <span class="m-0.5 inline-block translate-x-1 rounded-full p-0.5 text-neutral-400 transition-all group-hover/context:bg-neutral-200">
        <x-heroicon-c-ellipsis-vertical class="w-4" />
    </span>
</button>
