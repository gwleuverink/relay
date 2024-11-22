<?php

namespace App\Support\GitHub\Enums\Concerns;

trait HasHumanReadableValue
{
    public function forHumans(): string
    {
        return str($this->value)
            ->ucfirst()
            ->replace('_', ' ');
    }
}
