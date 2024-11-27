@props([
    'level' => 'primary',
    'type' => 'button',
    'href' => false,
])

@php
    $element = $href ? 'a' : 'button';

    $defaultClasses = 'cursor-default select-none rounded text-sm font-medium shadow-sm transition-all disabled:opacity-50';

    $levelClasses = match ($level) {
        'round' => 'rounded-full bg-indigo-600 p-1 text-white shadow-sm hover:scale-110 hover:bg-indigo-500 focus-visible:scale-110 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600',
        'danger' => 'bg-red-700 px-2 py-1 text-white hover:bg-red-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600',
        'primary' => 'bg-indigo-600 px-2 py-1 text-white hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600',
        'secondary' => 'bg-white px-2 py-1 text-neutral-500 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus-visible:outline-2 focus-visible:outline-gray-200 dark:bg-neutral-200 dark:text-neutral-700',
    };
@endphp

<{{ $element }} {{
    $attributes->merge([
        'type' => $type,
        'href' => $href,
        'class' => "{$defaultClasses} {$levelClasses}",
    ])
}}>{{ $slot }}</{{ $element }}>
