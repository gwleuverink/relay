<?php

namespace App\Livewire;

use App\Events\WorkflowRunDetected;
use App\Livewire\Concerns\WithGitHub;
use App\Models\WorkflowRun as RunModel;
use App\Support\GitHub\Enums\RunStatus;
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
        $this->run->updateFromRequest(
            $this->github->workflowRun($this->run->repository, $this->run->remote_id)
        );
    }

    public function restartJobs($runId)
    {
        $run = RunModel::find($runId);

        if (! $run) {
            return;
        }

        $this->run->updateQuietly([
            'status' => RunStatus::REQUESTED,
            'conclusion' => null,
        ]);

        $this->github->restartJobs($run->repository, $run->remote_id);

    }
}
