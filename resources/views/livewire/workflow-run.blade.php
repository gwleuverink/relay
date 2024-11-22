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

<article @if ($run->status->isRunning())
    wire:poll.5s="refresh"
@endif>
    <div @class([
        $colors,
        'border-l-4 bg-neutral-50 p-3',
        'opacity-70' => $run->status->isFinished(),
    ])>
        <div class="mb-2 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <div class="truncate font-medium text-neutral-700">{{ $run->repository }}</div>
                <div class="text-[10px] text-neutral-500">{{ '-' }}</div>
            </div>

            <div class="text-[10px] text-neutral-300">{{ $run->started_at_diff }}</div>
        </div>

        <div class="space-y-2">
            {{-- {{ $run->status->value }} --}}

            <div @class([
                'flex items-center space-x-3',
            ])>
                <x-support.status-icon
                    :status="$run->status"
                    :conclusion="$run->conclusion"
                />

                <div class="min-w-0 flex-grow">
                    <div class="truncate text-neutral-500">{{ $run->name }}</div>
                    <div class="truncate text-[9px] text-neutral-400">{{ $run->conclusion?->value ?? $run->status->value }}</div>
                </div>
            </div>
        </div>
    </div>
</article>
