<?php

namespace App\Livewire;

use App\Events\WorkflowRunDetected;
use App\Events\WorkflowStatusChanged;
use App\Livewire\Concerns\InteractsWithRun;
use App\Livewire\Concerns\WithGitHub;
use App\Models\WorkflowRun;
use Livewire\Component;

class RunDetail extends Component
{
    use InteractsWithRun;
    use WithGitHub;

    public WorkflowRun $run;

    protected $listeners = [
        'native:'.WorkflowStatusChanged::class => '$refresh',
        'native:'.WorkflowRunDetected::class => '$refresh',
    ];
}
