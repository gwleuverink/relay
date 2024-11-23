<div class="h-screen bg-neutral-100 font-sans antialiased backdrop-blur-xl">
    <div class="[-webkit-app-region: drag] z-50 h-7">
        <!-- Dragzone -->
    </div>

    {{-- START EXPERIMENT --}}
    <div class="mx-auto max-w-3xl space-y-3 px-4 py-2">
        <!-- Main Card -->
        <div class="overflow-hidden rounded-xl border border-gray-200/80 bg-white/80 shadow-sm">
            <!-- Header -->
            <div class="border-b border-gray-200/80 px-4 py-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="h-2 w-2 animate-pulse rounded-full bg-red-500"></div>

                        <h1 class="font-medium text-gray-900">{{ $run->repository }}</h1>
                    </div>

                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-500">#25</span>
                        <span class="rounded-md border border-red-200 bg-red-100 px-2 py-1 text-xs font-medium text-red-700">Failed</span>
                    </div>
                </div>
            </div>

            <!-- Info Grid -->
            <div class="grid grid-cols-2 divide-y divide-gray-200/80">
                <div class="border-r border-gray-200/80 px-4 py-2.5">
                    <div class="text-xs text-gray-500">Repository</div>
                    <div class="text-sm font-medium text-gray-900">gwleuverink/phost</div>
                </div>
                <div class="px-4 py-2.5">
                    <div class="text-xs text-gray-500">Branch</div>
                    <div class="truncate text-sm font-medium text-gray-900">update/native-child-processes</div>
                </div>
                <div class="border-r border-gray-200/80 px-4 py-2.5">
                    <div class="text-xs text-gray-500">Event</div>
                    <div class="text-sm font-medium text-gray-900">pull_request</div>
                </div>
                <div class="px-4 py-2.5">
                    <div class="text-xs text-gray-500">Commit</div>
                    <div class="font-mono text-sm font-medium text-gray-900">fbc8537</div>
                </div>
                <div class="border-r border-gray-200/80 px-4 py-2.5">
                    <div class="text-xs text-gray-500">Workflow</div>
                    <div class="text-sm font-medium text-gray-900">tests</div>
                </div>
                <div class="px-4 py-2.5">
                    <div class="text-xs text-gray-500">Attempt</div>
                    <div class="text-sm font-medium text-gray-900">6 of 6</div>
                </div>
                <div class="border-r border-gray-200/80 px-4 py-2.5">
                    <div class="text-xs text-gray-500">Started</div>
                    <div class="text-sm font-medium text-gray-900">Nov 22, 15:12:12</div>
                </div>
                <div class="px-4 py-2.5">
                    <div class="text-xs text-gray-500">Duration</div>
                    <div class="text-sm font-medium text-gray-900">2m 8s</div>
                </div>
            </div>

            <!-- Footer -->
            <div class="flex items-center justify-between border-t border-gray-200/80 bg-gray-50/80 px-4 py-3">
                <div class="flex items-center space-x-3">
                    <img
                        src="https://avatars.githubusercontent.com/u/17123491?v=4"
                        alt="gwleuverink"
                        class="h-6 w-6 rounded-full ring-2 ring-gray-200"
                    />
                    <span class="text-sm text-gray-600">gwleuverink</span>
                </div>
                <div class="flex space-x-2">
                    <button class="rounded-md border border-gray-200/80 bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-700 transition-colors hover:bg-gray-200">Re-run jobs</button>
                    <button class="rounded-md border border-gray-200/80 bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-700 transition-colors hover:bg-gray-200">View workflow</button>
                </div>
            </div>
        </div>

        <!-- Commit Card -->
        <div class="overflow-hidden rounded-xl border border-gray-200/80 bg-white/80 shadow-sm backdrop-blur-xl">
            <div class="border-b border-gray-200/80 px-4 py-3">
                <div class="text-xs text-gray-500">Commit Message</div>
                <div class="mt-1 text-sm font-medium text-gray-900">wip</div>
            </div>
            <div class="bg-gray-50/80 px-4 py-2">
                <div class="font-mono text-xs text-gray-500">fbc853734de4b9e8eabee7268b5009c6a52dfd9d</div>
            </div>
        </div>
    </div>
    {{-- END EXPERIMENT --}}
</div>
