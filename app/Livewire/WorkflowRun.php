<?php

namespace App\Livewire;

use App\Livewire\Concerns\WithGitHub;
use App\Models\WorkflowRun as RunModel;
use Livewire\Component;

class WorkflowRun extends Component
{
    use WithGitHub;

    public RunModel $run;

    public function refresh()
    {
        $data = $this->github->workflowRun($this->run->repository, $this->run->remote_id);
        $this->run->updateFromRequest($data);
    }
}
