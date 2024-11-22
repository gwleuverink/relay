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

    const PRUNE_AFTER_MINUTES = 60;

    public function github()
    {
        return resolve(GitHub::class);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->github()->runningWorkflows() as $repo => $runs) {
            foreach ($runs as $run) {
                WorkflowRun::updateOrCreateFromRequest($repo, fluent($run));
            }
        }

        $this->prune();
    }

    private function prune()
    {
        WorkflowRun::query()
            ->whereNotIn('status', [RunStatus::COMPLETED, RunStatus::SKIPPED])
            ->where('created_at', '<', now()->subMinutes(static::PRUNE_AFTER_MINUTES))
            ->get() // Don't mass delete - we need the model events to fire
            ->each->delete();
    }
}
