<?php

namespace App\Jobs;

use App\Models\WorkflowRun;
use App\Support\GitHub\Contracts\GitHub;
use App\Support\GitHub\Enums\RunStatus;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class FetchWorkflowRuns implements ShouldQueue
{
    use Queueable;

    const PRUNE_AFTER_MINUTES = 1;

    public function github()
    {
        return resolve(GitHub::class);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->github()->pendingActions() as $repo => $runs) {
            foreach ($runs as $run) {
                WorkflowRun::updateOrCreateFromRequest($repo, $run);
            }
        }

        // Prune old runs
        $runningStatusses = collect(RunStatus::cases())
            ->filter->isRunning()
            ->map->value;

        WorkflowRun::query()
            ->whereNotIn('status', $runningStatusses)
            ->where('created_at', '<', now()->subMinutes(static::PRUNE_AFTER_MINUTES))
            ->delete();
    }
}
