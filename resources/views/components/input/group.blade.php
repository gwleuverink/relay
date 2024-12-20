@props([
    'label' => false,
    'model' => false,
])

<label
    :id="$id('input')"
    class="relative mt-2 flex-grow"
>
    @if ($label)
        <label
            :for="$id('input')"
            @class([
                'absolute -top-2 left-2 inline-block select-none bg-white px-1 text-xs font-medium transition-colors dark:bg-neutral-950',
                'text-gray-900 dark:text-neutral-300' => $errors->missing($model),
                'text-red-700' => $errors->has($model),
            ])
        >
            {{ $label }}
        </label>
    @endif

    {{ $slot }}

    @error($model)
        <p
            class="my-1 text-xs text-red-600"
            wire:key="validation-message-{{ $model }}"
        >
            {{ $message }}
        </p>
    @enderror
</label>
