@props([
    'legend' => false,
    'bordered' => isset($bordered),
])

<fieldset @class([
    'relative mt-10 border-gray-300 pb-4 pt-6 dark:border-neutral-500',
    'rounded-md border px-3' => ! $bordered,
    'rounded-lg px-4 shadow-sm ring-1 ring-inset ring-gray-300' => $bordered,
])>
    <legend class="absolute -top-2.5 left-2 inline-block select-none bg-white px-1 text-sm font-medium text-gray-700 transition-colors dark:bg-neutral-950 dark:text-neutral-300">
        {{ $legend }}
    </legend>

    <div {{ $attributes->class('flex') }}>
        {{ $slot }}
    </div>
</fieldset>
