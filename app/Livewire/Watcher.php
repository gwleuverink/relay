<?php

namespace App\Livewire;

use App\Livewire\Concerns\WithGitHub;
use Livewire\Component;

class Watcher extends Component
{
    use WithGitHub;

    public function mount()
    {
        // $this->github->actions();
    }
}
