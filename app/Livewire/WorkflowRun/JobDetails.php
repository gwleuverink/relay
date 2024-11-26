<?php

namespace App\Livewire\WorkflowRun;

use App\Livewire\Concerns\WithGitHub;
use App\Models\WorkflowRun;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Component;

class JobDetails extends Component
{
    use WithGitHub;

    public WorkflowRun $run;

    public function refresh()
    {
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
}
