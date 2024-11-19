<?php

namespace App\Support\GitHub;

use App\Settings\Config;
use App\Support\GitHub\Contracts\Service;
use Illuminate\Http\Client\PendingRequest;

class Github implements Service
{
    const AUTH_URL = 'https://github.com/login/';

    const BASE_URL = 'https://api.github.com/';

    public function __construct(protected PendingRequest $gitHub, protected Config $config)
    {
        $this->gitHub->acceptJson();

        if ($config->accessToken) {
            $this->gitHub->withToken($config->github_access_token, 'token');
        }
    }

    /** @return array{verification_uri: string, device_code: string, user_code: string} */
    public function startUserVerification(): array
    {
        return $this->gitHub
            ->post(static::AUTH_URL.'device/code', [
                'client_id' => $this->config->github_client_id,
                'scope' => 'repo',
            ])->json();
    }

    public function getAccessToken(string $deviceCode): ?string
    {
        $response = $this
            ->gitHub
            ->post(static::AUTH_URL.'/oauth/access_token', [
                'client_id' => $this->config->github_client_id,
                'device_code' => $deviceCode,
                'grant_type' => 'urn:ietf:params:oauth:grant-type:device_code',
            ]);

        return $response->json('access_token');
    }

    /**
     * @return array{login: string}
     */
    public function getAuthorizedUser(string $accessToken): array
    {
        return $this
            ->gitHub
            ->withToken($accessToken)
            ->get(static::BASE_URL.'user')
            ->json();
    }
}
