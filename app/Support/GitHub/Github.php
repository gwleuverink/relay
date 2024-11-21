<?php

namespace App\Support\GitHub;

use App\Settings\Config;
use App\Support\GitHub\Contracts\GitHub as Service;
use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Collection;

class GitHub implements Service
{
    const AUTH_URL = 'https://github.com/login/';

    const BASE_URL = 'https://api.github.com/';

    const SCOPES = [
        'workflow',
        'read:org',
        'repo',
    ];

    public function __construct(
        protected PendingRequest $github,
        protected Config $config
    ) {
        $this->github->acceptJson();
        $this->github->throw();

        if ($config->github_access_token) {
            $this->github->withToken($config->github_access_token, 'token');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Data fetching
    |--------------------------------------------------------------------------
    */

    /** @var array[[id: string, name: string, full_name: string]] */
    public function repos(int $take = 50): Collection
    {
        $response = $this->github->post(static::BASE_URL.'graphql', [
            'query' => file_get_contents(__DIR__.'/queries/repositories.graphql'),
            'variables' => [
                'take' => $take,
            ],
        ])->json();

        if (array_key_exists('errors', $response)) {
            dd($response['errors']);
            throw new Exception('An error occured fetching data from GitHub');
        }

        $userRepos = data_get($response, 'data.viewer.repositories.nodes');
        $orgRepos = data_get($response, 'data.viewer.organizations.nodes.*.repositories.nodes.*');

        return collect($userRepos)
            ->merge($orgRepos)
            ->unique('nameWithOwner')
            ->sortByDesc('pushedAt');
    }

    public function actions(): array
    {
        return [];
        // TODO: Refactor so we can limit amount fetched (latest 10 repo's should suffice)
        $repositories = $this->repos()->pluck('nameWithOwner');

        // dd($repositories);

        $response = $this->github
            ->post(static::BASE_URL.'graphql', [
                'query' => file_get_contents(__DIR__.'/queries/workflow-runs.graphql'),
                'variables' => [
                    'repos' => $repositories,
                ],
            ]);

        dd($response->json());
    }

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    */

    /** @return array{verification_uri: string, device_code: string, user_code: string} */
    public function startUserVerification(): array
    {
        return $this->github
            ->post(static::AUTH_URL.'device/code', [
                'client_id' => config('services.github.client_id'),
                'scope' => implode(', ', static::SCOPES),
            ])->json();
    }

    public function getAccessToken(string $deviceCode): ?string
    {
        $response = $this->github
            ->post(static::AUTH_URL.'/oauth/access_token', [
                'device_code' => $deviceCode,
                'client_id' => config('services.github.client_id'),
                'grant_type' => 'urn:ietf:params:oauth:grant-type:device_code',
            ]);

        // Set token for subsequent requests
        $this->github->withToken(
            $token = $response->json('access_token')
        );

        return $token;
    }

    /** @return array{login: string} */
    public function authorizedUser(?string $accessToken = null): array
    {
        $accessToken = $accessToken
            ? $accessToken
            : $this->config->github_access_token;

        return $this->github
            ->withToken($accessToken)
            ->get(static::BASE_URL.'user')
            ->json();
    }
}
