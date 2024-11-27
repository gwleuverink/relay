<?php

namespace App\Jobs;

use App\Settings\Config;
use App\Models\WorkflowRun;
use App\Support\GitHub\Contracts\GitHub;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Support\GitHub\Enums\ConclusionStatus;

class FetchWorkflowRuns implements ShouldQueue
{
    use Queueable;

    const PRUNE_AFTER_MINUTES = 60;

    public function handle(GitHub $github, Config $config): void
    {

        if (! $config->github_access_token || ! $config->github_username) {
            return;
        }

        foreach ($github->runningWorkflows() as $repo => $runs) {
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

        WorkflowRun::onlyTrashed()->forceDelete();
    }
}
