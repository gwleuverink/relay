@use(App\Support\GitHub\Enums\RunStatus)
@use(App\Support\GitHub\Enums\ConclusionStatus)

@php
    $colors = match ($run->status) {
        default => 'border-transparent',
        RunStatus::QUEUED => 'border-amber-400',
        RunStatus::IN_PROGRESS => 'border-blue-500',
    };

    $colors = match ($run->conclusion) {
        default => $colors,
        ConclusionStatus::FAILURE => 'border-red-500',
        ConclusionStatus::SUCCESS => 'border-green-400',
    };
@endphp

<article
    @if ($run->status->isRunning())
        wire:poll.5s="refresh"
    @endif
    :wire:key="$run->remote_id"
>
    <div @class([
        $colors,
        'group border-l-4 bg-neutral-50 p-3',
        'opacity-70 transition-opacity hover:opacity-100' => $run->status->isFinished(),
    ])>
        <div class="mb-2 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <div class="truncate font-medium text-neutral-700">{{ $run->repository }}</div>
                <div class="text-[10px] text-neutral-500">{{ '-' }}</div>
            </div>

            <div class="text-[10px] text-neutral-400">{{ $run->started_at_diff }}</div>
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
                    <button
                        type="button"
                        class="transi cursor-default rounded-full p-0.5 opacity-20 transition-all hover:bg-neutral-200 group-hover:opacity-100"
                    >
                        <x-heroicon-c-ellipsis-vertical class="w-4 text-neutral-500" />
                    </button>
                </div>
            </div>
        </div>
    </div>
</article>
