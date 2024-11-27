<?php

namespace App\Livewire\WorkflowRun;

use Livewire\Component;
use App\Models\WorkflowRun;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Computed;
use App\Events\WorkflowStatusChanged;
use App\Livewire\Concerns\WithGitHub;

class JobDetails extends Component
{
    use WithGitHub;

    public WorkflowRun $run;

    protected $listeners = [
        'native:'.WorkflowStatusChanged::class => '$refresh',
    ];

    public function refresh()
    {
        $this->run->refresh();

        $response = $this->github->workflowJobs(
            $this->run->repository,
            $this->run->remote_id
        );

        if(count($response['jobs']) === 0) {
            return;
        }

        $this->run->update([
            'jobs' => $response['jobs']
        ]);
    }

    public function hasRunningJobs(): bool
    {
        if(!$this->run->jobs) {
            return false;
        }

        return $this->run->jobs->contains(
            fn($job) => $job->status->isRunning()
        );
    }
}
