<?php

namespace App\Livewire;

use App\Livewire\Concerns\WithConfig;
use App\Livewire\Concerns\WithGitHub;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Settings extends Component
{
    const MAX_REPOSITORIES = 15;

    use WithConfig;
    use WithGitHub;

    #[Validate('array|max:'.self::MAX_REPOSITORIES)]
    public array $selectedRepositories = [];

    public function mount()
    {
        $this->selectedRepositories = $this->config->github_selected_repositories;
    }

    /*
    |--------------------------------------------------------------------------
    | Hooks
    |--------------------------------------------------------------------------
    */
    public function updatedSelectedRepositories($value)
    {
        if (count($this->selectedRepositories) > static::MAX_REPOSITORIES) {
            $indexToRemove = array_search($value, $this->selectedRepositories);

            if ($indexToRemove) {
                unset($this->selectedRepositories[$indexToRemove]);
            }
        }

        $this->config->fill([
            'github_selected_repositories' => $this->selectedRepositories,
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
            'repositories',
            now()->addMinutes(5),
            fn () => $this->github->repos()
        );

        // Only unselected repo's
        return $repositories->reject(function ($repo) {
            return in_array($repo['nameWithOwner'], $this->selectedRepositories);
        });
    }
}
