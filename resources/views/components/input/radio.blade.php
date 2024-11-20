@use(Illuminate\View\ComponentSlot)

@props(['value', 'type' => 'radio', 'label' => false, 'description' => false])

@php
    $model = $attributes->wire('model')->value;
@endphp

<div
    x-id="['input']"
    class="relative flex items-center"
    {{ $attributes->whereStartsWith(['wire:key']) }}
>
    <div class="flex h-6 items-center">
        <input
            {{ $attributes->whereStartsWith(['wire', 'x', 'disabled', 'readonly']) }}
            value="{{ $value ?? null }}"
            :id="$id('input')"
            type="{{ $type }}"
            @class([
                'h-4 w-4 rounded-full text-indigo-600 read-only:ring-opacity-50',
                'border-neutral-200 ring-neutral-200 focus:ring-indigo-600' => $errors->missing(
                    $model
                ),
                'border-red-400 ring-red-400 focus:ring-red-500' => $errors->has($model),
            ])
            @if (!$attributes->get('name')) name="{{ $attributes->get('wire:model') }}" @endif
        />
    </div>

    <div class="ml-2 select-none text-sm leading-6">
        @if ($label)
            <label
                :for="$id('input')"
                @class([
                    'text-xs font-medium',
                    $label instanceof ComponentSlot ? $label->attributes->get('class') : null,
                    'text-neutral-500' => $errors->missing($model),
                    'text-red-700' => $errors->has($model),
                ])
            >
                {{ $label }}
            </label>
        @endif

        @if ($description)
            <p class="text-gray-500">
                {{ $description }}
            </p>
        @endif
    </div>
</div>
