<?php

namespace App\Livewire\Concerns;

use App\Models\WorkflowRun;
use Native\Laravel\Facades\Window;
use Livewire\Attributes\Renderless;

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

    #[Renderless]
    public function logout()
    {
        $this->config->fill([
            'github_username' => null,
            'github_access_token' => null,
            'github_selected_repositories' => [],
        ])->save();

        $this->clearRuns();
        $this->clearCaches();

        return $this->redirectRoute('login', navigate: true);
    }

    #[Renderless]
    public function clearCaches()
    {
        cache()->clear();
    }

    #[Renderless]
    public function clearRuns()
    {
        Window::close('detail-window');
        WorkflowRun::truncate();

        return $this->redirectRoute('watcher', navigate: true);
    }
}
