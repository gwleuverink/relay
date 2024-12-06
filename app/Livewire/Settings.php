<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Illuminate\Support\Collection;
use App\Livewire\Concerns\WithConfig;
use App\Livewire\Concerns\WithGitHub;

class Settings extends Component
{
    const MAX_REPOSITORIES = 15;

    use WithConfig;
    use WithGitHub;

    #[Validate('bool')]
    public bool $pollRecentPushes;

    #[Validate('array|max:' . self::MAX_REPOSITORIES)]
    public array $selectedRepositories = [];

    public function mount()
    {
        $this->pollRecentPushes = $this->config->github_poll_by_recent_push;
        $this->selectedRepositories = $this->config->github_selected_repositories;
    }

    /*
    |--------------------------------------------------------------------------
    | Hooks
    |--------------------------------------------------------------------------
    */
    public function updatedSelectedRepositories(mixed $value = null)
    {
        if (count($this->selectedRepositories) > static::MAX_REPOSITORIES) {
            $indexToRemove = array_search($value, $this->selectedRepositories);

            if ($indexToRemove) {
                unset($this->selectedRepositories[$indexToRemove]);
            }
        }

        $this->togglePollRecentPushesProperty();

        $this->config->fill([
            'github_poll_by_recent_push' => $this->pollRecentPushes,
            'github_selected_repositories' => $this->selectedRepositories,
        ])->save();
    }

    public function updatedPollRecentPushes($checked)
    {
        $this->config->fill([
            'github_poll_by_recent_push' => $this->pollRecentPushes,
        ])->save();
    }

    /*
    |--------------------------------------------------------------------------
    | Computed
    |--------------------------------------------------------------------------
    */
    #[Computed()]
    public function repositories(): Collection
    {
        $repositories = cache()->remember(
            'settings-repository-list',
            now()->addMinutes(50),
            fn () => $this->github->repos(100)
        );

        // Only unselected repo's
        return $repositories->reject(function ($repo) {
            return in_array($repo['nameWithOwner'], $this->selectedRepositories);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Support
    |--------------------------------------------------------------------------
    */
    private function togglePollRecentPushesProperty()
    {
        // The recentPushes setting should always be enabled
        // whenever no specific repositories were selected
        if (! $this->selectedRepositories) {
            $this->pollRecentPushes = true;
        }
    }
}
