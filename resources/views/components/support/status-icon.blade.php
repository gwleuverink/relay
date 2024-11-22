@use(App\Support\GitHub\Enums\RunStatus)
@use(App\Support\GitHub\Enums\ConclusionStatus)

@props([
    'status',
    'conclusion',
])

@php
    $colors = match ($status) {
        default => 'bg-slate-300',
        RunStatus::QUEUED => 'bg-amber-500',
        RunStatus::IN_PROGRESS => 'bg-blue-500',
    };

    $colors = match ($conclusion) {
        default => $colors,
        ConclusionStatus::FAILURE => 'bg-red-500',
        ConclusionStatus::SUCCESS => 'bg-green-500',
    };
@endphp

<div>
    @if ($status->isRunning())
        <x-svg.loading class="m-1 w-4 text-neutral-500" />
    @else
        <div @class([
            $colors,
            'flex h-6 w-6 items-center justify-center rounded-full text-white',
        ])>
            @if ($conclusion === ConclusionStatus::SUCCESS)
                <x-heroicon-c-check class="w-4" />
            @elseif ($conclusion === ConclusionStatus::FAILURE)
                <x-heroicon-c-x-mark class="w-4" />
            @else
                <x-heroicon-c-circle-stack class="w-4" />
            @endif
        </div>
    @endif
</div>
