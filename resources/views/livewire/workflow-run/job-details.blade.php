@use(App\Support\GitHub\Enums\RunStatus)
@use(App\Support\GitHub\Enums\ConclusionStatus)

<div
    wire:init="refresh"
    @if ($run->status->isRunning())
        wire:poll.10s="refresh"
    @endif
    class="bg-slate-200 shadow-inner shadow-slate-300 border border-slate-300 p-8 rounded-xl flex flex-col"
>

    <div x-init="autoAnimate($el)" class="space-y-5 max-w-sm">

        @foreach($run->jobs as $job)

            @php
                $pingColor = match ($job->status) {
                    default => 'bg-neutral-300',
                    RunStatus::IN_PROGRESS => 'bg-blue-500',
                    RunStatus::QUEUED, RunStatus::PENDING, RunStatus::REQUESTED => 'bg-amber-400',
                };

                $pingColor = match ($job->conclusion) {
                    default => $pingColor,
                    ConclusionStatus::FAILURE => 'bg-red-500',
                    ConclusionStatus::SUCCESS => 'bg-green-500',
                };
            @endphp

            <div
                wire:key="job-{{ $job->name }}"
                x-on:close-runs.window="expanded = false"
                x-data="{
                    expanded: false,
                    toggle: function() {
                        if(!this.expanded) {
                            $dispatch('close-runs')
                        }

                        $nextTick(
                            () => this.expanded = !this.expanded
                        )
                    }
                }"
                class="overflow-hidden rounded-lg border border-gray-200/75 bg-neutral-50 shadow-sm transition-shadow duration-200 hover:shadow-md"
            >
                <button x-on:click="toggle" type="button" class="p-3 w-full flex items-center">
                    <div class="flex space-x-2 items-center">
                        <div class="size-1.5 rounded-full {{ $pingColor }}"></div>
                        <h3 class="truncate text-sm font-medium text-gray-800">{{ $job->name }}</h3>
                    </div>

                    <div class="ml-auto flex items-center space-x-2 text-xs">

                        @if($job->status->isRunning())
                            <x-svg.loading x-show="! expanded" class="w-3 text-slate-300" />
                        @else

                            <span class="text-gray-400">
                                @php
                                    $diff = $job->started_at->diff($job->completed_at);
                                    $format = ($diff->m > 0 ? '%im ' : '') . '%ss';
                                    $format = ($diff->h > 0 ? '%hh ' : '') . $format;
                                @endphp

                                {{ $diff->format($format) }}
                            </span>
                        @endif


                        <x-heroicon-o-chevron-right class="size-3 text-gray-400 transition-transform" ::class="{ 'rotate-90': expanded }"  stroke-width="2" />
                    </div>
                </button>

                {{-- Timeline --}}
                <div x-show="expanded" x-init="autoAnimate($el)" x-collapse class="space-y-3">

                    {{-- Steps --}}
                    @foreach($job->steps as $step)

                        @php
                            $pingColor = match ($step->status) {
                                default => 'bg-neutral-300',
                                RunStatus::IN_PROGRESS => 'bg-blue-500',
                                RunStatus::QUEUED, RunStatus::PENDING, RunStatus::REQUESTED => 'bg-amber-400',
                            };

                            $pingColor = match ($step->conclusion) {
                                default => $pingColor,
                                ConclusionStatus::FAILURE => 'bg-red-500',
                                ConclusionStatus::SUCCESS => 'bg-green-500',
                            };
                        @endphp

                        <div wire:key="step-{{ $step->name }}" class="flex items-center text-xs ml-[0.8rem] mr-8">
                            <div class="h-1 w-1 rounded-full {{ $pingColor }}"></div>
                            <span class="ml-3 text-gray-600">{{ $step->name }}</span>

                            @if($step->status && $step->status->isRunning())
                                <x-svg.loading class="size-3 text-slate-300 ml-auto" />
                            @else
                                @php
                                    $diff = $step->started_at->diff($step->completed_at);

                                    $format = ($diff->m > 0 ? '%im ' : '') . '%ss';
                                    $format = ($diff->h > 0 ? '%hh ' : '') . $format;
                                @endphp

                                <span class="ml-auto text-gray-400">{{ $diff->format($format) }}</span>
                            @endif
                        </div>

                    @endforeach

                    {{-- Failure notice --}}
                    @if($job->conclusion === ConclusionStatus::FAILURE)
                        <div class="mx-4 rounded bg-red-100 p-2 text-xs">
                            <div class="space-x-1 flex items-center font-medium text-red-600">
                                <x-heroicon-o-exclamation-triangle class="size-3" stroke-width="2" />
                                <span>Run Failed</span>
                            </div>
                            {{-- <div class="leading-snug text-red-600/75">Error in Feature/Commands/InstallTest.php</div> --}}
                        </div>
                    @endif

                    {{-- Footer --}}
                    <div class="border-t border-gray-150 bg-gray-100 px-3 py-2 mt-2">
                        <div class="flex items-center text-xs text-gray-500">
                            <x-heroicon-o-bolt class="mr-1.5 size-3" stroke-width="2" />
                            <span>{{ $job->runner_name }}</span>

                            <span class="ml-auto flex items-center">

                                <x-heroicon-o-clock class="mr-1.5 size-3" stroke-width="2" />
                                <span>Started {{ $job->started_at->format('H:i:s') }}</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

    </div>

    @if($run->jobs->isEmpty())
        <x-svg.loading class="w-5 text-slate-400 self-center" />
    @endif

</div>

    {{-- @dump($run->jobs) --}}
    {{-- <!-- Workflow Header -->
    <div class="mb-4 px-1">
        <div class="flex items-center space-x-3">
            <h1 class="text-base font-medium text-gray-600">tests</h1>
            <div class="flex items-center space-x-2 text-xs text-gray-500">
                <span class="flex items-center">
                    <svg
                        class="mr-1 h-3 w-3"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"
                        />
                    </svg>
                    #40
                </span>
                <span class="flex items-center">
                    <svg
                        class="mr-1 h-3 w-3"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                        />
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                        />
                    </svg>
                    update/native-child-processes
                </span>
            </div>
        </div>
    </div>

    <!-- Jobs List -->
    <div class="space-y-2">
        <!-- Skip Duplicates Job (Collapsed) -->
        <div class="overflow-hidden rounded-lg border border-gray-200/75 bg-white shadow-sm transition-shadow duration-200 hover:shadow-md">
            <div class="p-3">
                <div class="flex items-center">
                    <div class="flex min-w-0 items-center">
                        <div class="mr-2 h-1.5 w-1.5 rounded-full bg-green-500"></div>
                        <h3 class="truncate text-sm font-medium text-gray-800">skip-duplicates</h3>
                    </div>
                    <div class="ml-auto flex items-center space-x-2 text-xs">
                        <span class="text-gray-400">2s</span>
                        <div class="flex h-4 items-center">
                            <svg
                                class="h-3 w-3 text-gray-400"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 5l7 7-7 7"
                                />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Laravel Tests Job (Expanded) -->
        <div class="overflow-hidden rounded-lg border border-gray-200/75 bg-white shadow-sm transition-shadow duration-200 hover:shadow-md">
            <div class="p-3">
                <div class="mb-3 flex items-center">
                    <div class="flex min-w-0 items-center">
                        <div class="mr-2 h-1.5 w-1.5 rounded-full bg-red-500"></div>
                        <h3 class="truncate text-sm font-medium text-gray-800">laravel-tests</h3>
                    </div>
                    <div class="ml-auto flex items-center space-x-2 text-xs">
                        <span class="text-gray-400">44s</span>
                        <div class="flex h-4 items-center">
                            <svg
                                class="h-3 w-3 text-gray-400"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 9l-7 7-7-7"
                                />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Timeline -->
                <div class="space-y-2 border-l-2 border-gray-100 pl-4">
                    <div class="flex items-center text-xs">
                        <div class="-ml-[0.3125rem] h-1 w-1 rounded-full bg-green-500"></div>
                        <span class="ml-3 text-gray-600">Set up job</span>
                        <span class="ml-auto text-gray-400">1s</span>
                    </div>
                    <div class="flex items-center text-xs">
                        <div class="-ml-[0.3125rem] h-1 w-1 rounded-full bg-green-500"></div>
                        <span class="ml-3 text-gray-600">
                            Run actions/checkout
                            @v3
                        </span>
                        <span class="ml-auto text-gray-400">0s</span>
                    </div>
                    <div class="flex items-center text-xs">
                        <div class="-ml-[0.3125rem] h-1 w-1 rounded-full bg-green-500"></div>
                        <span class="ml-3 text-gray-600">
                            Run shivammathur/setup-php
                            @v2
                        </span>
                        <span class="ml-auto text-gray-400">9s</span>
                    </div>
                    <div class="flex items-center text-xs">
                        <div class="-ml-[0.3125rem] h-1 w-1 rounded-full bg-green-500"></div>
                        <span class="ml-3 text-gray-600">Cache Composer dependencies</span>
                        <span class="ml-auto text-gray-400">1s</span>
                    </div>
                    <div class="flex items-center text-xs">
                        <div class="-ml-[0.3125rem] h-1 w-1 rounded-full bg-green-500"></div>
                        <span class="ml-3 text-gray-600">
                            Run php-actions/composer
                            @v6
                        </span>
                        <span class="ml-auto text-gray-400">25s</span>
                    </div>
                    <div class="flex items-center text-xs">
                        <div class="-ml-[0.3125rem] h-1 w-1 rounded-full bg-red-500"></div>
                        <span class="ml-3 text-gray-600">Execute tests</span>
                        <span class="ml-auto text-gray-400">3s</span>
                    </div>

                    <!-- Failed Step Details -->
                    <div class="mt-2 rounded bg-red-50 p-2 text-xs">
                        <div class="mb-1 flex items-center font-medium text-red-600">
                            <svg
                                class="mr-1 h-3 w-3"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                                />
                            </svg>
                            Test Failure
                        </div>
                        <div class="leading-snug text-red-600/75">Error in Feature/Commands/InstallTest.php</div>
                    </div>
                </div>
            </div>

            <!-- Runner Info -->
            <div class="border-t border-gray-100 bg-gray-50/50 px-3 py-2">
                <div class="flex items-center text-xs text-gray-500">
                    <svg
                        class="mr-1.5 h-3 w-3"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z"
                        />
                    </svg>
                    GitHub Actions 19
                    <span class="ml-auto flex items-center">
                        <svg
                            class="mr-1 h-3 w-3"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                        Started 23:48:14
                    </span>
                </div>
            </div>
        </div>
    </div> --}}
