@use(App\Support\GitHub\Enums\RunStatus)
@use(App\Support\GitHub\Enums\ConclusionStatus)

@php
    $colors = match ($run->status) {
        default => 'border-neutral-300',
        RunStatus::IN_PROGRESS => 'border-blue-500',
        RunStatus::QUEUED, RunStatus::PENDING, RunStatus::REQUESTED => 'border-amber-400',
    };

    $colors = match ($run->conclusion) {
        default => $colors,
        ConclusionStatus::FAILURE => 'border-red-500',
        ConclusionStatus::SUCCESS => 'border-green-400',
    };
@endphp

<article
    class="overflow-hidden"
    @if ($run->status->isRunning())
        {{-- sync state with GitHub --}}
        wire:poll.5s="refresh"
    @else
        {{-- Update timestamps --}}
        wire:poll.60s
    @endif
>
    <div @class([
        $colors,
        'group border-l-4 bg-neutral-50 p-3',
        'opacity-70 transition-opacity hover:opacity-100' => $run->status->isFinished(),
    ])>
        <div class="mb-2 flex items-center justify-between space-x-2">
            <div class="space-x-2 truncate font-medium text-neutral-700">
                {{ $run->repository }}
            </div>

            <div class="flex-shrink-0 text-[10px] text-neutral-400">
                {{ $run->started_at_diff }}
            </div>
        </div>

        <div class="space-y-2">
            <div @class([
                'flex items-center space-x-3',
            ])>
                <x-support.status-icon
                    :status="$run->status"
                    :conclusion="$run->conclusion"
                />

                <div class="min-w-0 flex-grow">
                    <div class="truncate text-neutral-500">{{ $run->name }}</div>
                    <div class="truncate text-[9px] text-neutral-400">{{ $run->conclusion?->forHumans() ?? $run->status->forHumans() }}</div>
                </div>

                <div>
                    <x-support.run-context-menu :$run />
                </div>
            </div>
        </div>
    </div>
</article>
