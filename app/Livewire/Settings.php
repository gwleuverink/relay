<?php

namespace App\Livewire;

use App\Livewire\Concerns\WithConfig;
use App\Support\GitHub\Aggregators\Repository;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Settings extends Component
{
    const MAX_REPOSITORIES = 3;

    use WithConfig;

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
    public function repositories()
    {
        $repositories = Repository::aggregate();

        // Only unselected repo's
        return collect($repositories)->reject(function ($repo) {
            return in_array($repo['full_name'], $this->selectedRepositories);
        });
    }
}
