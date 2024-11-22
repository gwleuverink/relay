<?php

namespace App\Jobs;

use App\Models\WorkflowRun;
use App\Support\GitHub\Contracts\GitHub;
use App\Support\GitHub\Enums\ConclusionStatus;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class FetchWorkflowRuns implements ShouldQueue
{
    use Queueable;

    const PRUNE_AFTER_MINUTES = 60;

    public function handle(): void
    {
        logger()->info('Fetching Workflow runs...');

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
            ->whereIn('conclusion', [ConclusionStatus::SUCCESS])
            ->where('created_at', '<', now()->subMinutes(static::PRUNE_AFTER_MINUTES))
            ->get() // Don't mass delete - we need the model events to fire
            ->each->delete();
    }

    private function github()
    {
        return resolve(GitHub::class);
    }
}
