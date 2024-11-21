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

    public bool $all;

    #[Validate('array|max:'.self::MAX_REPOSITORIES)]
    public array $selectedRepositories = [];

    public function mount()
    {
        $this->selectedRepositories = $this->config->github_selected_repositories;

        $this->toggleAllProperty();
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

        $this->config->fill([
            'github_selected_repositories' => $this->selectedRepositories,
        ])->save();

        $this->toggleAllProperty();
    }

    public function updatedAll($checked)
    {
        if ($checked) {
            $this->selectedRepositories = [];
            $this->updatedSelectedRepositories();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Computed
    |--------------------------------------------------------------------------
    */
    #[Computed()]
    public function repositories(): Collection
    {
        // dd($this->github->repos(100)->count());
        // $repositories = $this->github->repos(100);

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
    private function toggleAllProperty()
    {
        $this->all = $this->selectedRepositories
            ? false
            : true;
    }
}
