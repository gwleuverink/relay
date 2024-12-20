@use(Illuminate\View\ComponentSlot)

@props([
    'header' => '',
    'fixed' => isset($fixed),
])

<div class="relative min-h-screen bg-neutral-100 text-xs">
    <div @class([
        'fixed left-0 right-0 top-0 z-10 grid h-7 border border-b border-b-neutral-300/40 bg-gradient-to-r from-neutral-100 to-neutral-200 pt-0.5 font-semibold text-neutral-500 shadow-sm',
        '[-webkit-app-region:drag]' => ! $fixed,
    ])>
        @if ($header instanceof ComponentSlot)
            <div {{ $header->attributes->merge(['class' => 'flex items-center px-1']) }}>
                {{ $header }}
            </div>
        @else
            <div class="flex items-center">{{ $header }}</div>
        @endif
    </div>

    <div class="absolute bottom-0 left-0 right-0 top-7 overflow-y-auto overflow-x-hidden">
        {{ $slot }}
    </div>
</div>
