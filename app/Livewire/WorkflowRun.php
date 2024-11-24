<?php

namespace App\Livewire;

use App\Events\WorkflowStatusChanged;
use App\Livewire\Concerns\InteractsWithRun;
use App\Livewire\Concerns\WithGitHub;
use App\Models\WorkflowRun as RunModel;
use Livewire\Component;
use Native\Laravel\Facades\Window;

class WorkflowRun extends Component
{
    use InteractsWithRun;
    use WithGitHub;

    public RunModel $run;

    protected $listeners = [
        'native:'.WorkflowStatusChanged::class => '$refresh',
    ];

    public function refresh()
    {
        $this->run->updateFromRequest(
            $this->github->workflowRun($this->run->repository, $this->run->remote_id)
        );
    }

    public function viewRun()
    {
        Window::open('run-detail')
            ->route('run-detail', [$this->run])
            ->titleBarHiddenInset()
            ->resizable(false)
            ->focusable()
            ->width(500)
            ->height(500);
    }
}
