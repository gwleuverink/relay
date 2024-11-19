<?php

namespace App\Livewire;

use App\Livewire\Concerns\WithConfig;
use App\Livewire\Concerns\WithGitHub;
use Livewire\Attributes\Renderless;
use Livewire\Component;
use Native\Laravel\Facades\Notification;

class Auth extends Component
{
    use WithConfig;
    use WithGitHub;

    public ?string $userCode = null;

    public ?string $deviceCode = null;

    public string $verificationUri = 'https://github.com/login/device';

    public function startUserVerification(): void
    {
        $response = $this->github->startUserVerification();

        $this->deviceCode = $response['device_code'];
        $this->userCode = $response['user_code'];
        $this->verificationUri = $response['verification_uri'];
    }

    #[Renderless]
    public function pollAuthorization()
    {
        if (! $this->userCode || ! $this->deviceCode) {
            return;
        }

        $accessToken = $this->github->getAccessToken($this->deviceCode);

        if (! $accessToken) {
            return;
        }

        $user = $this->github->getAuthorizedUser($accessToken);

        $this->config->fill([
            'github_access_token' => $accessToken,
            'github_username' => $user['login'],
        ])->save();

        Notification::title('Action Monitor Connected')
            ->message("Connected with {$user['login']}")
            ->show();

        return $this->redirectRoute('watcher', navigate: true);
    }
}
