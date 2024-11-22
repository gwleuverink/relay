@use(App\Support\GitHub\Enums\RunStatus)

@props([
    'type' => 'idle',
    'repo' => '',
    'trigger' => '',
    'triggeredAt' => 'now'
])

@php
    $classes = match($type) {
        default => 'border-slate-300',
        'failed' => 'border-red-500',
        'in_progress' => 'border-blue-500',
    }
@endphp

<article>

    <div {{ $attributes }} @class([
        $classes,
        'border-l-4 border-blue-500 bg-neutral-50 p-3'
    ])>
        <div class="mb-2 flex items-center justify-between">

            <div class="flex items-center space-x-2">
                <div class="truncate font-medium text-neutral-900">{{ $repo }}</div>
                <div class="text-[10px] text-neutral-500">{{ $trigger }}</div>
            </div>

            <div class="text-[10px] text-neutral-500">{{ $triggeredAt }}</div>
        </div>

        <div class="space-y-2">
            {{ $slot }}
        </div>
    </div>

</article>
