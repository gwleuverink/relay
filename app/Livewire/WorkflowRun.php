<?php

namespace App\Livewire;

use App\Events\WorkflowRunDetected;
use App\Livewire\Concerns\WithGitHub;
use App\Models\WorkflowRun as RunModel;
use Livewire\Component;

class WorkflowRun extends Component
{
    use WithGitHub;

    public RunModel $run;

    protected $listeners = [
        'native:'.WorkflowRunDetected::class => '$refresh',
    ];

    public function refresh()
    {
        $data = $this->github->workflowRun($this->run->repository, $this->run->remote_id);
        $this->run->updateFromRequest($data);
    }
}
