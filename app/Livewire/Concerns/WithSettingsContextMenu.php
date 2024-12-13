<?php

namespace App\Livewire\Concerns;

use App\Models\WorkflowRun;

trait WithSettingsContextMenu
{
    use WithConfig;

    public function resetSelection()
    {
        $this->fill([
            'pollRecentPushes' => true,
            'selectedRepositories' => [],
        ]);

        $this->updatedPollRecentPushes(true);
        $this->updatedSelectedRepositories(null);
    }

    public function logout()
    {
        $this->config->fill([
            'github_username' => null,
            'github_access_token' => null,
            'github_selected_repositories' => [],
        ])->save();

        return $this->redirectRoute('login', navigate: true);
    }

    public function clearCaches()
    {
        cache()->clear();
    }

    public function clearRuns()
    {
        WorkflowRun::truncate();

        return $this->redirectRoute('watcher', navigate: true);
    }
}
