<?php

namespace App\Livewire;

use App\Events\WorkflowRunDetected;
use App\Events\WorkflowRunPruned;
use App\Livewire\Concerns\WithGitHub;
use App\Models\WorkflowRun;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Watcher extends Component
{
    use WithGitHub;

    protected $listeners = [
        'native:'.WorkflowRunDetected::class => '$refresh',
        'native:'.WorkflowRunPruned::class => '$refresh',
    ];

    #[Computed()]
    public function runs()
    {
        // \App\Jobs\FetchWorkflowRuns::dispatchSync();

        return WorkflowRun::latest()->get();
    }
}
