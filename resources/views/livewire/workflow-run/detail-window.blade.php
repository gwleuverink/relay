@use(App\Support\GitHub\Enums\RunStatus)
@use(App\Support\GitHub\Enums\ConclusionStatus)

@php
    $pingColor = match ($run->status) {
        default => 'bg-neutral-300',
        RunStatus::IN_PROGRESS => 'bg-blue-500',
        RunStatus::QUEUED, RunStatus::PENDING, RunStatus::REQUESTED => 'bg-amber-400',
    };

    $pingColor = match ($run->conclusion) {
        default => $pingColor,
        ConclusionStatus::FAILURE => 'bg-red-500',
        ConclusionStatus::SUCCESS => 'bg-green-500',
    };
@endphp

<x-layouts.window>
    <div class="mx-auto max-w-3xl space-y-6 px-4 pb-6 pt-4">
        {{-- Detail card --}}
        <div class="overflow-hidden rounded-xl border border-gray-200/80 bg-white/80 shadow-sm">
            <!-- Header -->
            <div class="border-b border-gray-200/80 px-4 py-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div @class([
                            $pingColor,
                            'size-2 rounded-full',
                            'animate-pulse' => $run->status->isRunning(),
                        ])></div>
                        <h1 class="font-medium text-gray-800">{{ $run->data->display_title }}</h1>
                    </div>

                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-500">#{{ $run->data->run_number }}</span>
                        <x-support.status-badge
                            :status="$run->status"
                            :conclusion="$run->conclusion"
                        />
                    </div>
                </div>
            </div>

            {{-- Info Grid --}}
            <div class="grid grid-cols-2 gap-px divide-gray-200/80 overflow-hidden bg-gray-200/80 *:bg-white">
                <a
                    x-open-external
                    href="{{ $run->data->repository->html_url }}"
                    class="group px-4 py-2.5 ring-inset ring-indigo-200 focus:outline-none focus:ring-2"
                >
                    <div class="flex justify-between text-xs text-gray-500">
                        Repository
                        <x-heroicon-c-link class="size-3 opacity-0 transition-opacity group-hover:opacity-80" />
                    </div>

                    <div class="text-sm font-medium text-gray-700/90">
                        {{ $run->data->repository->full_name }}
                    </div>
                </a>

                <a
                    x-open-external
                    href="{{ "{$run->data->repository->html_url}/tree/{$run->data->head_branch}" }}"
                    class="group px-4 py-2.5 ring-inset ring-indigo-200 focus:outline-none focus:ring-2"
                >
                    <div class="flex justify-between text-xs text-gray-500">
                        Branch
                        <x-heroicon-c-link class="size-3 opacity-0 transition-opacity group-hover:opacity-80" />
                    </div>

                    <div class="truncate text-sm font-medium text-gray-700/90">
                        {{ $run->data->head_branch }}
                    </div>
                </a>

                <div class="px-4 py-2.5">
                    <div class="flex justify-between text-xs text-gray-500">
                        Event
                        <x-heroicon-c-link class="size-3 opacity-0 transition-opacity group-hover:opacity-80" />
                    </div>

                    <div class="text-sm font-medium text-gray-700/90">
                        {{ str($run->data->event)->replace('_', ' ') }}
                    </div>
                </div>

                <a
                    x-open-external
                    href="{{ "{$run->data->repository->html_url}/commit/{$run->data->head_commit->id}" }}"
                    class="group px-4 py-2.5 ring-inset ring-indigo-200 focus:outline-none focus:ring-2"
                >
                    <div class="flex justify-between text-xs text-gray-500">
                        Commit
                        <x-heroicon-c-link class="size-3 opacity-0 transition-opacity group-hover:opacity-80" />
                    </div>
                    <div class="font-mono text-sm font-medium text-gray-700/90">{{ str($run->data->head_sha)->limit(7, '') }}</div>
                </a>

                <div class="px-4 py-2.5">
                    <div class="text-xs text-gray-500">Workflow</div>
                    <div class="text-sm font-medium text-gray-700/90">{{ $run->data->name }}</div>
                </div>

                <div class="px-4 py-2.5">
                    <div class="text-xs text-gray-500">Started</div>
                    <div class="text-sm font-medium text-gray-700/90">{{ $run->started_at->format('M j, H:i:s') }}</div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-between border-t border-gray-200/80 bg-gray-50/80 px-4 py-3">
                <a
                    x-open-external
                    href="{{ $run->data->triggering_actor->html_url }}"
                    class="flex -translate-x-1.5 items-center space-x-3 rounded-md px-1.5 py-1 ring-inset ring-indigo-200 focus:outline-none focus:ring-2"
                >
                    <img
                        src="{{ $run->data->triggering_actor->avatar_url }}"
                        alt="{{ $run->data->triggering_actor->login }}"
                        class="h-6 w-6 rounded-full ring-2 ring-gray-200"
                    />
                    <span class="text-sm text-gray-600">{{ $run->data->triggering_actor->login }}</span>
                </a>

                <div class="flex space-x-2">
                    @if ($run->canRestart())
                        <button
                            x-on:click="
                                $contextMenu([
                                    {
                                        label: 'Re-run all jobs',
                                        click: async () => $wire.restartJobs(),
                                    },
                                    {
                                        label: 'Re-run failed jobs',
                                        click: async () => $wire.restartFailedJobs(),
                                    },
                                ])
                            "
                            class="cursor-default rounded-md border border-gray-200/80 bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-700 ring-inset ring-indigo-200 transition-colors hover:bg-gray-200 focus:outline-none focus:ring-2"
                        >
                            Re-run
                        </button>
                    @endif

                    @if ($run->canCancel())
                        <button
                            wire:click="cancelRun"
                            class="cursor-default rounded-md border border-gray-200/80 bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-700 ring-inset ring-indigo-200 transition-colors hover:bg-gray-200 focus:outline-none focus:ring-2"
                        >
                            Cancel
                        </button>
                    @endif

                    @if ($run->canDelete())
                        <button
                            wire:click="deleteRun"
                            class="cursor-default rounded-md border border-gray-200/80 bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-700 ring-inset ring-indigo-200 transition-colors hover:bg-gray-200 focus:outline-none focus:ring-2"
                        >
                            Stop tracking
                        </button>
                    @endif

                    <a
                        x-open-external
                        href="{{ $run->data->html_url }}"
                        class="cursor-default rounded-md border border-gray-200/80 bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-700 ring-inset ring-indigo-200 transition-colors hover:bg-gray-200 focus:outline-none focus:ring-2"
                    >
                        Open in GitHub
                    </a>
                </div>
            </div>
        </div>

        {{-- Workflow jobs --}}
        <livewire:workflow-run.job-details :$run />

        {{-- Commit card --}}
        <div class="overflow-hidden rounded-xl border border-gray-200/80 bg-white/80 shadow-sm backdrop-blur-xl">
            <div class="border-b border-gray-200/80 px-4 py-3">
                <div class="text-xs text-gray-500">Commit Message</div>
                <div class="mt-1 text-sm font-medium text-gray-800">{{ $run->data->head_commit->message }}</div>
            </div>
            <div class="bg-gray-50/80 px-4 py-2">
                <div class="font-mono text-xs text-gray-500">{{ $run->data->head_commit->id }}</div>
            </div>
        </div>
    </div>
</x-layouts.window>
