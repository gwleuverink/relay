@use(App\Support\GitHub\Enums\RunStatus)

@php
    $classes = match($run->status) {
        default => 'border-slate-300',
        RunStatus::FAILURE => 'border-red-500',
        RunStatus::QUEUED => 'border-amber-400',
        RunStatus::COMPLETED => 'border-green-400',
        RunStatus::IN_PROGRESS => 'border-blue-500',
    }
@endphp

<article
    @if($run->status->isRunning())
        wire:poll.5s="refresh"
    @endif
>

    <div @class([
        $classes,
        'border-l-4 bg-neutral-50 p-3'
    ])>

        <div class="mb-2 flex items-center justify-between">

            <div class="flex items-center space-x-2">
                <div class="truncate font-medium text-neutral-900">{{ $run->repository }}</div>
                <div class="text-[10px] text-neutral-500">{{ '-' }}</div>
            </div>

            <div class="text-[10px] text-neutral-300">{{ $run->started_at_diff }}</div>
        </div>

        <div class="space-y-2">
            {{-- {{ $run->status->value }} --}}

            <div @class([
                'flex items-center space-x-3',
                'opacity-70' => $run->status->isCompleted()
            ])>
                <x-support.status-icon :status="$run->status" />

                <div class="min-w-0 flex-grow">
                    <div class="truncate text-neutral-500">{{ $run->name }}</div>
                    <div class="truncate text-[9px] text-neutral-400">ubuntu-latest • Node 16</div>
                </div>
            </div>
        </div>
    </div>

</article>
