<?php

namespace App\Livewire\WorkflowRun;

use Livewire\Component;
use Native\Laravel\Facades\Window;
use App\Events\WorkflowStatusChanged;
use App\Livewire\Concerns\WithGitHub;
use App\Models\WorkflowRun as RunModel;
use App\Livewire\Concerns\InteractsWithRun;

class ListItem extends Component
{
    use InteractsWithRun;
    use WithGitHub;

    public RunModel $run;

    protected $listeners = [
        // TODO: We can toggle renderless based on run id later
        'native:' . WorkflowStatusChanged::class => '$refresh',
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
        usleep(300); // Sometimes the window closes after being opened due to a race condition

        Window::open('detail-window')
            ->route('detail-window', [$this->run])
            ->showDevTools(false)
            ->maximizable(false)
            ->titleBarHidden()
            ->focusable()
            // -- width --
            ->width(700)
            ->minWidth(560)
            ->maxWidth(800)
            // -- height --
            ->height(600)
            ->minHeight(600);
    }
}
