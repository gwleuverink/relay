@props([
    'status' => 'queued',
    'name' => '',
    'environment' => '',
])

@php
    $classes = match($status){
        default => '',
        'skipped', 'finished' => 'opacity-60',
    }
@endphp

<div @class([
    $classes,
    'flex items-center space-x-3'
])>
    <div class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-500 text-[10px] text-white">🔧</div>
    <div class="min-w-0 flex-grow">
        <div class="truncate text-neutral-500">{{ $name }}</div>
        <div class="truncate text-[9px] text-neutral-400">{{ $environment }}</div>
    </div>
</div>
