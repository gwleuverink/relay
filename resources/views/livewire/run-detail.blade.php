<div class="h-screen bg-neutral-100 font-sans antialiased backdrop-blur-xl">
    <div class="[-webkit-app-region: drag] z-50 h-7">
        <!-- Dragzone -->
    </div>

    <div class="mx-auto max-w-3xl space-y-3 px-4 py-2">
        <!-- Main Card -->
        <div class="overflow-hidden rounded-xl border border-gray-200/80 bg-white/80 shadow-sm">
            <!-- Header -->
            <div class="border-b border-gray-200/80 px-4 py-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="h-2 w-2 animate-pulse rounded-full bg-red-500"></div>
                        <h1 class="font-medium text-gray-900">{{ $run->data->display_title }}</h1>
                    </div>

                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-500">#{{ $run->data->run_number }}</span>
                        <span class="rounded-md border border-red-200 bg-red-100 px-2 py-1 text-xs font-medium text-red-700">
                            {{ $run->conclusion?->forHumans() ?? $run->status->forHumans() }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Info Grid -->
            <div class="grid grid-cols-2 gap-px divide-gray-200/80 overflow-hidden bg-gray-200/80 *:bg-white">
                <a
                    x-open-external
                    href="{{ $run->data->repository->html_url }}"
                    class="px-4 py-2.5"
                >
                    <div class="text-xs text-gray-500">Repository</div>
                    <div class="text-sm font-medium text-gray-900">{{ $run->data->repository->full_name }}</div>
                </a>
                <a
                    x-open-external
                    href="{{ "{$run->data->repository->html_url}/tree/{$run->data->head_branch}" }}"
                    class="px-4 py-2.5"
                >
                    <div class="text-xs text-gray-500">Branch</div>
                    <div class="truncate text-sm font-medium text-gray-900">{{ $run->data->head_branch }}</div>
                </a>
                <div class="px-4 py-2.5">
                    <div class="text-xs text-gray-500">Event</div>
                    <div class="text-sm font-medium text-gray-900">{{ $run->data->event }}</div>
                </div>
                <a
                    x-open-external
                    href="{{ "{$run->data->repository->html_url}/commit/{$run->data->head_commit->id}" }}"
                    class="px-4 py-2.5"
                >
                    <div class="text-xs text-gray-500">Commit</div>
                    <div class="font-mono text-sm font-medium text-gray-900">{{ str($run->data->head_sha)->limit(7, '') }}</div>
                </a>
                <div class="px-4 py-2.5">
                    <div class="text-xs text-gray-500">Workflow</div>
                    <div class="text-sm font-medium text-gray-900">{{ $run->data->name }}</div>
                </div>
                <div class="px-4 py-2.5">
                    <div class="text-xs text-gray-500">Attempt</div>
                    <div class="text-sm font-medium text-gray-900">{{ "{$run->data->run_attempt} of {$run->data->run_attempt}" }}</div>
                </div>
                <div class="px-4 py-2.5">
                    <div class="text-xs text-gray-500">Started</div>
                    <div class="text-sm font-medium text-gray-900">{{ $run->started_at->format('M j, H:i:s') }}</div>
                </div>
                <div class="px-4 py-2.5">
                    <div class="text-xs text-gray-500">Duration</div>
                    <div class="text-sm font-medium text-gray-900">2m 8s</div>
                </div>
            </div>

            <!-- Footer -->
            <div class="flex items-center justify-between border-t border-gray-200/80 bg-gray-50/80 px-4 py-3">
                <a
                    x-open-external
                    href="{{ $run->data->triggering_actor->html_url }}"
                    class="flex items-center space-x-3"
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
                            class="rounded-md border border-gray-200/80 bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-700 transition-colors hover:bg-gray-200"
                        >
                            Re-run jobs
                        </button>
                    @endif

                    @if ($run->canCancel())
                        <button
                            wire:click="cancelRun"
                            class="rounded-md border border-gray-200/80 bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-700 transition-colors hover:bg-gray-200"
                        >
                            Cancel Run
                        </button>
                    @endif

                    <a
                        x-open-external
                        href="{{ $run->data->html_url }}"
                        class="rounded-md border border-gray-200/80 bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-700 transition-colors hover:bg-gray-200"
                    >
                        View workflow
                    </a>
                </div>
            </div>
        </div>

        <!-- Commit Card -->
        <div class="overflow-hidden rounded-xl border border-gray-200/80 bg-white/80 shadow-sm backdrop-blur-xl">
            <div class="border-b border-gray-200/80 px-4 py-3">
                <div class="text-xs text-gray-500">Commit Message</div>
                <div class="mt-1 text-sm font-medium text-gray-900">{{ $run->data->head_commit->message }}</div>
            </div>
            <div class="bg-gray-50/80 px-4 py-2">
                <div class="font-mono text-xs text-gray-500">{{ $run->data->head_commit->id }}</div>
            </div>
        </div>
    </div>
</div>
