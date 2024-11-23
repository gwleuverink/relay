<?php

namespace App\Livewire;

use App\Events\WorkflowRunDetected;
use App\Livewire\Concerns\WithGitHub;
use App\Models\WorkflowRun as RunModel;
use App\Support\GitHub\Enums\RunStatus;
use Livewire\Component;
use Native\Laravel\Facades\Window;

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

    public function restartJobs()
    {
        $this->run->updateQuietly([
            'status' => RunStatus::REQUESTED,
            'conclusion' => null,
        ]);

        $this->github->restartJobs(
            $this->run->repository,
            $this->run->remote_id
        );
    }

    public function restartFailedJobs()
    {
        $this->run->updateQuietly([
            'status' => RunStatus::REQUESTED,
            'conclusion' => null,
        ]);

        $this->github->restartFailedJobs(
            $this->run->repository,
            $this->run->remote_id
        );
    }

    public function viewRun()
    {
        Window::open('run-detail')
            ->route('run-detail', [$this->run])
            ->titleBarHidden()
            // ->frameless()
            // ->lightVibrancy()
            ->width(500)
            ->height(600);
    }
}
