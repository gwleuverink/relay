<?php

namespace App\Livewire\Concerns;

use App\Support\GitHub\Contracts\GitHub;
use Livewire\Attributes\Computed;

trait WithGitHub
{
    #[Computed()]
    public function github(): GitHub
    {
        return resolve(GitHub::class);
    }
}
