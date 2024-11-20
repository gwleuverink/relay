@props(['value', 'type' => 'checkbox', 'label' => false, 'description' => false])

<x-input.radio
    {{ $attributes }}
    :$value
    :$type
    :$label
    :$description
/>
