<?php

namespace App\Livewire;

use App\Events\WorkflowRunDetected;
use App\Models\WorkflowRun;
use Livewire\Component;

class RunDetail extends Component
{
    public WorkflowRun $run;

    protected $listeners = [
        'native:'.WorkflowRunDetected::class => '$refresh',
    ];

    public function render()
    {
        return view('livewire.run-detail');
    }
}
