@use(App\Support\GitHub\Enums\RunStatus)
@use(App\Support\GitHub\Enums\ConclusionStatus)

@php
    if (! function_exists('pingColor')) {

        function pingColor($status, $conclusion)
        {
            $color = match ($status) {
                default => 'bg-neutral-300',
                RunStatus::IN_PROGRESS => 'bg-blue-500',
                RunStatus::QUEUED, RunStatus::PENDING, RunStatus::REQUESTED => 'bg-amber-400',
            };

            return match ($conclusion) {
                default => $color,
                ConclusionStatus::FAILURE => 'bg-red-500',
                ConclusionStatus::SUCCESS => 'bg-green-500',
            };
        }
    }
@endphp

<div
    wire:init="refresh"
    @if ($this->hasRunningJobs())
        wire:poll.10s="refresh"
    @endif
    class="flex flex-col rounded-xl border border-slate-300/80 bg-slate-200 p-8 shadow-inner shadow-slate-300/80"
>
    <div
        x-init="autoAnimate($el)"
        class="max-w-sm space-y-5"
    >
        @foreach ($run->jobs as $job)
            <div
                wire:key="job-{{ $job->name }}"
                x-on:close-runs.window="expanded = false"
                x-data="{
                    expanded: false,
                    toggle: function () {
                        if (! this.expanded) {
                            $dispatch('close-runs')
                        }

                        $nextTick(() => (this.expanded = ! this.expanded))
                    },
                }"
                class="overflow-hidden rounded-lg border border-gray-300/80 bg-neutral-50 shadow-sm ring-indigo-200 transition-shadow duration-200 focus-within:ring-1 hover:shadow-md"
            >
                <button
                    x-on:click="toggle"
                    type="button"
                    class="flex w-full items-center rounded-lg p-3 focus:outline-none"
                >
                    <div class="flex items-center space-x-2">
                        <div class="{{ pingColor($job->status, $job->conclusion) }} size-1.5 rounded-full"></div>
                        <h3 class="truncate text-sm font-medium text-gray-800">{{ $job->name }}</h3>
                    </div>

                    <div class="ml-auto flex items-center space-x-2 text-xs">
                        @if ($job->status->isRunning())
                            <x-svg.loading
                                x-show="! expanded"
                                class="w-3 text-slate-300"
                            />
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

                        <x-heroicon-o-chevron-right
                            class="size-3 text-gray-400 transition-transform"
                            ::class="{ 'rotate-90': expanded }"
                            stroke-width="2"
                        />
                    </div>
                </button>

                {{-- Timeline --}}
                <div
                    x-show="expanded"
                    x-init="autoAnimate($el)"
                    x-collapse
                    x-cloak
                    class="space-y-3"
                >
                    {{-- Steps --}}
                    @foreach ($job->steps as $step)
                        <div
                            wire:key="step-{{ $step->name }}"
                            class="ml-[0.8rem] mr-8 flex items-center text-xs"
                        >
                            <div class="{{ pingColor($step->status, $step->conclusion) }} h-1 w-1 rounded-full"></div>
                            <span class="ml-3 text-gray-600">{{ $step->name }}</span>

                            @if ($step->status && $step->status->isRunning())
                                <x-svg.loading class="ml-auto size-3 text-slate-300" />
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
                    @if ($job->conclusion === ConclusionStatus::FAILURE)
                        <div class="mx-4 rounded bg-red-100 p-2 text-xs">
                            <div class="flex items-center space-x-1 font-medium text-red-600">
                                <x-heroicon-o-exclamation-triangle
                                    class="-mb-0.5 size-3"
                                    stroke-width="2"
                                />
                                <span>Run Failed</span>
                            </div>
                            {{-- <div class="leading-snug text-red-600/75">Error in Feature/Commands/InstallTest.php</div> --}}
                        </div>
                    @endif

                    {{-- Footer --}}
                    <div class="border-gray-150 mt-2 border-t bg-gray-100 px-2.5 py-2">
                        <div class="flex items-center text-[10px] text-gray-500/80">
                            @if ($job->runner_name)
                                <x-heroicon-c-bolt
                                    class="mr-1.5 size-3 text-gray-400"
                                    stroke-width="2"
                                />
                                <span>{{ $job->runner_name }}</span>
                            @endif

                            <span class="ml-auto flex items-center">
                                <x-heroicon-o-clock
                                    class="mr-1.5 size-3"
                                    stroke-width="2"
                                />
                                <span class="text-opacity-75">Started {{ $job->started_at->format('H:i') }}</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if ($run->jobs->isEmpty())
        <x-svg.loading class="w-5 self-center text-slate-400" />
    @endif
</div>
