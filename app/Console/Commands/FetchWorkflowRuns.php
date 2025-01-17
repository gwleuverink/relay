<?php

namespace App\Console\Commands;

use App\Settings\Config;
use App\Models\WorkflowRun;
use Illuminate\Console\Command;
use App\Support\GitHub\Contracts\GitHub;
use App\Support\GitHub\Enums\ConclusionStatus;

class FetchWorkflowRuns extends Command
{
    const PRUNE_AFTER_MINUTES = 180;
    const REPOSITORIES_CACHE_KEY = 'polling-repositories';

    protected $signature = 'relay:fetch-runs';

    protected $description = 'Fetch currently active runs by repository configured by the user.';

    protected GitHub $github;
    protected Config $config;

    public function handle(
        GitHub $github,
        Config $config
    ): void {

        $this->github = $github;
        $this->config = $config;

        if (! $config->github_access_token || ! $config->github_username) {
            return;
        }

        $workflows = $github->runningWorkflows(
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
                static::REPOSITORIES_CACHE_KEY,
                now()->addMinutes(5),
                fn () => $this->github->repos(15)
                    // TODO: Remove repos with old pushed_at? Not relevant or - add to repos query?
                    // ->reject()
                    ->pluck('nameWithOwner')
                    ->values()
                    ->toArray()
            );
        }

        return array_unique(array_merge($selectedRepos, $recentRepos));
    }

    private function prune()
    {
        // Don't prune when debug mode is enabled
        if (app()->hasDebugModeEnabled() && ! app()->runningUnitTests()) {
            return;
        }

        // First prune all trashed runs
        WorkflowRun::onlyTrashed()->forceDelete();

        // Then softdelete newer runs. They will be pruned the next time
        // This way there is ample time for open detail windows to close
        // Otherwise these will throw a 404 exception.
        WorkflowRun::query()
            ->whereIn('conclusion', [
                ConclusionStatus::SUCCESS,
                ConclusionStatus::COMPLETED,
                ConclusionStatus::CANCELLED,
            ])
            ->where('created_at', '<', now()->subMinutes(static::PRUNE_AFTER_MINUTES))
            ->get() // Don't mass delete - we need the model events to fire
            ->each->delete();
    }

    public static function clearCachedRepositories(): void
    {
        cache()->forget(static::REPOSITORIES_CACHE_KEY);
    }
}
