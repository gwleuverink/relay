<?php

namespace App\Livewire;

use App\Jobs\FetchWorkflowRuns;
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

        $user = $this->github->authorizedUser($accessToken);

        $this->config->fill([
            'github_access_token' => $accessToken,
            'github_username' => $user['login'],
            'github_selected_repositories' => [], // -> reset when reauthenticating
        ])->save();

        Notification::title('Action Monitor Connected')
            ->message("Connected with {$user['login']}")
            ->show();

        FetchWorkflowRuns::dispatch();

        return $this->redirectRoute('watcher', navigate: true);
    }
}
