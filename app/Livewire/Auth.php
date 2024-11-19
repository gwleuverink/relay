<?php

namespace App\Livewire;

use App\Settings\Config;
use App\Support\GitHub\Contracts\GitHub;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Renderless;
use Livewire\Component;
use Native\Laravel\Facades\Notification;

class Auth extends Component
{
    public ?string $deviceCode = null;

    public ?string $userCode = null;

    public function mount()
    {
        \Native\Laravel\Facades\MenuBar::show();
    }

    public function startUserVerification(): void
    {
        $response = $this->github->startUserVerification();

        $this->deviceCode = $response['device_code'];
        $this->userCode = $response['user_code'];
    }

    // #[Renderless]
    public function pollAuthorization()
    {
        if (! $this->userCode || ! $this->deviceCode) {
            $this->js("console.log('NO DEVICE CODE')");

            return;
        }

        $accessToken = $this->github->getAccessToken($this->deviceCode);

        if (! $accessToken) {
            $this->js("console.log('NO TOKEN')");

            return;
        }

        $user = $this->github->getAuthorizedUser($accessToken);

        $this->js("console.log('AUTHENTICATING!')");

        $this->config->fill([
            'github_access_token' => $accessToken,
            'github_username' => $user['login'],
        ])->save();

        Notification::title('Action Monitor authenticated')
            ->message("Logged in as {$user['login']}")
            ->show();

        return $this->redirectRoute('watcher', navigate: true);
    }

    #[Computed()]
    public function github(): Github
    {
        return resolve(GitHub::class);
    }

    #[Computed()]
    public function config(): Config
    {
        return resolve(Config::class);
    }
}
