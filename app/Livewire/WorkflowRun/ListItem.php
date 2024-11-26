<?php

namespace App\Livewire\WorkflowRun;

use App\Events\WorkflowStatusChanged;
use App\Livewire\Concerns\InteractsWithRun;
use App\Livewire\Concerns\WithGitHub;
use App\Models\WorkflowRun as RunModel;
use Livewire\Component;
use Native\Laravel\Facades\Window;

class ListItem extends Component
{
    use InteractsWithRun;
    use WithGitHub;

    public RunModel $run;

    protected $listeners = [
        // TODO: We can toggle renderless based on run id later
        'native:'.WorkflowStatusChanged::class => '$refresh',
    ];

    public function refresh()
    {
        if (! $this->run->status->isRunning()) {
            return;
        }

        $this->run->updateFromRequest(
            $this->github->workflowRun($this->run->repository, $this->run->remote_id)
        );
    }

    public function viewRun()
    {
        Window::close('detail-window');

        Window::open('detail-window')
            ->route('detail-window', [$this->run])
            ->showDevTools(false)
            ->titleBarHidden()
            ->resizable(false)
            ->focusable()
            ->width(700)
            ->height(600);
    }
}
