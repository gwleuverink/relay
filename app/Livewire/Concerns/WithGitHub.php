<?php

namespace App\Livewire\Concerns;

use Livewire\Attributes\Computed;
use App\Support\GitHub\Contracts\GitHub;

trait WithGitHub
{
    #[Computed()]
    public function github(): GitHub
    {
        return resolve(GitHub::class);
    }
}
