<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\WorkflowRun;
use App\Jobs\FetchWorkflowRuns;
use Livewire\Attributes\Renderless;
use App\Livewire\Concerns\WithConfig;
use App\Livewire\Concerns\WithGitHub;
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

        Notification::title('Workflow Monitor Connected')
            ->message("Connected with {$user['login']}")
            ->show();

        WorkflowRun::truncate();
        FetchWorkflowRuns::dispatch();

        return $this->redirectRoute('watcher', navigate: true);
    }
}
