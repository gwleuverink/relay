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

    public function __construct(
        protected GitHub $github,
        protected Config $config
    ) {}

    public function handle(): void
    {

        if (! $this->config->github_access_token || ! $this->config->github_username) {
            return;
        }

        $workflows = $this->github->runningWorkflows(
            $this->repositories()
        );

        foreach ($workflows as $repo => $runs) {
            foreach ($runs as $run) {
                WorkflowRun::updateOrCreateFromRequest($repo, fluent($run));
            }
        }

        $this->prune();
    }

    private function repositories(): array
    {

        $recentRepos = [];
        $selectedRepos = $this->config->github_selected_repositories;

        if ($this->config->github_poll_by_recent_push) {
            $recentRepos = cache()->remember(
                'pending-actions-repository-list',
                now()->addMinutes(5),
                fn () => $this->github->repos(10)
                    // TODO: Remove repos with old pushed_at? Not relevant or - add to repos query?
                    // ->reject()
                    ->pluck('nameWithOwner')
                    ->values()
                    ->toArray()
            );
        }

        return array_unique($selectedRepos + $recentRepos);
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
