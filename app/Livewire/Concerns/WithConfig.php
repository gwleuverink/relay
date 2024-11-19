<?php

namespace App\Livewire\Concerns;

use App\Settings\Config;
use Livewire\Attributes\Computed;

trait WithConfig
{
    #[Computed()]
    public function config(): Config
    {
        return resolve(Config::class);
    }
}
