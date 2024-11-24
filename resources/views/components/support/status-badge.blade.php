@use(App\Support\GitHub\Enums\RunStatus)
@use(App\Support\GitHub\Enums\ConclusionStatus)

@props([
    'status',
    'conclusion',
])

@php
    $colors = match ($status) {
        default => 'border-neutral-200 bg-neutral-100 text-neutral-700',
        RunStatus::QUEUED => 'border-amber-200 bg-amber-100 text-amber-700/90',
        RunStatus::IN_PROGRESS => 'border-blue-500/40 bg-blue-400/20 text-blue-800/80',
    };

    $colors = match ($conclusion) {
        default => $colors,
        ConclusionStatus::FAILURE => 'border-red-200 bg-red-100 text-red-700',
        ConclusionStatus::SUCCESS => 'border-emerald-600/50 bg-emerald-400/20 text-emerald-700',
    };
@endphp

<span @class([
    $colors,
    'flex items-center space-x-2 rounded-md border px-2 py-1 text-xs font-medium',
])>
    @if ($status->isRunning())
        <x-svg.loading class="w-3.5" />
    @endif

    <span>
        {{ $conclusion?->forHumans() ?? $status->forHumans() }}
    </span>
</span>
