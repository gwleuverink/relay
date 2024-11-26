<?php

namespace App\Livewire\WorkflowRun;

use Livewire\Component;
use App\Models\WorkflowRun;
use App\Events\WorkflowRunPruned;
use Native\Laravel\Facades\Window;
use App\Events\WorkflowRunDetected;
use App\Events\WorkflowStatusChanged;
use App\Livewire\Concerns\WithGitHub;
use App\Livewire\Concerns\InteractsWithRun;

class DetailWindow extends Component
{
    use InteractsWithRun;
    use WithGitHub;

    public WorkflowRun $run;

    protected $listeners = [
        'native:'.WorkflowStatusChanged::class => '$refresh',
        'native:'.WorkflowRunPruned::class => 'pruned',
    ];

    public function pruned($id)
    {
        if($id === $this->run->id) {
            Window::close('detail-window');
        }
    }
}
