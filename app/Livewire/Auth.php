<?php

namespace App\Livewire;

use App\Support\GitHub\Contracts\GitHub;
use Livewire\Component;

class Auth extends Component
{
    public ?string $deviceCode = null;

    public function fetchDeviceCode(): void {}

    public function github(GitHub $github): Github
    {
        return $github;
    }

    public function render()
    {
        return view('livewire.auth');
    }
}
