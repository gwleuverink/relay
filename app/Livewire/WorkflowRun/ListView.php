<?php

namespace App\Livewire\WorkflowRun;

use Livewire\Component;
use App\Models\WorkflowRun;
use App\Events\WorkflowRunPruned;
use Livewire\Attributes\Computed;
use App\Events\WorkflowRunDetected;
use App\Support\WindowHeightManager;
use App\Events\WorkflowStatusChanged;
use App\Livewire\Concerns\WithGitHub;

class ListView extends Component
{
    use WithGitHub;

    protected $listeners = [
        'native:'.WorkflowStatusChanged::class => '$refresh',
        'native:'.WorkflowRunDetected::class => '$refresh',
        'native:'.WorkflowRunPruned::class => '$refresh',
    ];

    #[Computed()]
    public function runs()
    {
        return WorkflowRun::query()
            ->latest()->get()
            // Can't update window height after open - Maybe a good PR?
            // ->tap(
            //     fn ($runs) => WindowHeightManager::handle($runs->count())
            // )
            ->sortBy(
                fn ($run) => $run->sortWeight()
            );
    }
}
