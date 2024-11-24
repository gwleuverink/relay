<?php

namespace App\Observers;

use App\Events\WorkflowRunDetected;
use App\Events\WorkflowRunPruned;
use App\Events\WorkflowStatusChanged;
use App\Models\WorkflowRun;

class WorkflowRunObserver
{
    /**
     * Handle the WorkflowRun "created" event.
     */
    public function created(WorkflowRun $run): void
    {
        WorkflowRunDetected::dispatch($run);
    }

    /**
     * Handle the WorkflowRun "updated" event.
     */
    public function updated(WorkflowRun $run): void
    {
        // if ($run->status->isRunning() !== $run->getOriginal('status')->isRunning()) { // The running status changed
        //     if ($run->status->isRunning()) { // And is now running
        //         WorkflowRunDetected::dispatch($run);
        //     }
        // }

        if ($run->status !== $run->getOriginal('status') || $run->conclusion !== $run->getOriginal('conclusion')) {
            WorkflowStatusChanged::dispatch($run);
        }
    }

    /**
     * Handle the WorkflowRun "deleted" event.
     */
    public function deleted(WorkflowRun $run): void
    {
        WorkflowRunPruned::dispatch($run);
    }

    /**
     * Handle the WorkflowRun "restored" event.
     */
    public function restored(WorkflowRun $run): void
    {
        //
    }

    /**
     * Handle the WorkflowRun "force deleted" event.
     */
    public function forceDeleted(WorkflowRun $run): void
    {
        //
    }
}
