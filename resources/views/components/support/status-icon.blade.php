@use(App\Support\GitHub\Enums\RunStatus)

@props([
    'status',
])

@php
    $colors = match ($status) {
        default => 'bg-slate-300',
        RunStatus::FAILURE => 'bg-red-500',
        RunStatus::QUEUED => 'bg-amber-500',
        RunStatus::COMPLETED => 'bg-green-500',
        RunStatus::IN_PROGRESS => 'bg-blue-500',
    }
@endphp

<div>
    @if ($status->isRunning())
        <x-svg.loading class="w-6 text-neutral-500" />
    @else

        <div @class([
            $colors,
            'flex h-6 w-6 items-center justify-center rounded-full text-white',
        ])>
            @if ($status === RunStatus::FAILURE)
                <x-heroicon-c-x-mark class="w-4" />
            @elseif ($status === RunStatus::COMPLETED)
                <x-heroicon-c-check class="w-4" />
            @else
                <x-heroicon-c-circle-stack class="w-4" />
            @endif
        </div>

    @endif
</div>
