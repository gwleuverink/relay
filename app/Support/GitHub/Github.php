<?php

namespace App\Support\GitHub;

use App\Settings\Config;
use App\Support\GitHub\Contracts\GitHub as Service;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Concurrency;
use Illuminate\Support\Facades\Http;

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
    public function repos(): array
    {
        return $this
            ->github
            ->get(static::BASE_URL.'user/repos', [
                'sort' => 'pushed',
                'per_page' => 30,
            ])->json();
    }

    public function actions(): array
    {
        /** @var array[[id: string, name: string, full_name: string]] */
        $repos = cache()->remember('repositories', now()->addMinutes(10), fn () => $this->repos());

        $urls = array_map(fn ($repo) => static::BASE_URL."repos/{$repo['full_name']}/actions/runs", $repos);

        $responses = collect($urls)
            ->chunk(3)
            ->flatMap(function ($chunk) {
                $responses = Http::pool(fn ($pool) => $chunk->map(function ($url) use ($pool) {
                    return $pool->acceptJson()->throw()->get($url);
                }));

                sleep(1); // Optional delay between chunks

                return $responses;
            });

        dd($responses);

        // $actions = Http::pool(fn (Pool $pool) => array_map(
        //     fn ($repo) => $pool->acceptJson()->throw()->get(static::BASE_URL."repos/{$repo['full_name']}/actions/runs"),
        //     $repos
        // ));

        // dd($actions);

        // [$userActions, $orgActions] = Concurrency::run([
        //     fn () => '',
        //     fn () => '',
        // ]);
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
        $response = $this
            ->github
            ->post(static::AUTH_URL.'/oauth/access_token', [
                'device_code' => $deviceCode,
                'client_id' => config('services.github.client_id'),
                'grant_type' => 'urn:ietf:params:oauth:grant-type:device_code',
            ]);

        return $response->json('access_token');
    }

    /** @return array{login: string} */
    public function authorizedUser(?string $accessToken = null): array
    {
        $accessToken = $accessToken
            ? $accessToken
            : $this->config->github_access_token;

        return $this
            ->github
            ->withToken($accessToken)
            ->get(static::BASE_URL.'user')
            ->json();
    }
}
