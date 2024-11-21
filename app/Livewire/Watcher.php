<?php

namespace App\Livewire;

use App\Jobs\FetchWorkflowRuns;
use App\Livewire\Concerns\WithGitHub;
use Livewire\Component;

class Watcher extends Component
{
    use WithGitHub;

    public function mount()
    {
        // FetchWorkflowRuns::dispatchSync();
    }
}
