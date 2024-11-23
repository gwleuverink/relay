<?php

namespace App\Support\GitHub;

use App\Settings\Config;
use App\Support\GitHub\Contracts\GitHub as Service;
use App\Support\GitHub\Enums\RunStatus;
use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Fluent;

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
        logger()->info('Fetching repositories...');

        // TODO: Consider filtering pushed_at date with max age
        $response = $this->github->post(static::BASE_URL.'graphql', [
            'query' => file_get_contents(__DIR__.'/queries/repositories.graphql'),
            'variables' => [
                'take' => $take,
            ],
        ])->json();

        if (array_key_exists('errors', $response)) {
            throw new Exception('An error occured fetching data from GitHub');
        }

        $userRepos = data_get($response, 'data.viewer.repositories.nodes');
        $orgRepos = data_get($response, 'data.viewer.organizations.nodes.*.repositories.nodes.*');

        return collect($userRepos)
            ->merge($orgRepos)
            ->unique('nameWithOwner')
            ->sortByDesc('pushedAt');
    }

    public function runningWorkflows(): Collection
    {
        logger()->info('Fetching Workflow runs...');

        $responses = collect();

        // Fetch 10 repositories for the user & all organization - (n * 10)
        $repositories = cache()->remember(
            'pending-actions-repository-list',
            now()->addMinutes(5),
            fn () => $this->repos(10)
                // TODO: Remove repos with old pushed_at? Not relevant or - add to repos query?
                // ->reject()
                ->pluck('nameWithOwner')
                ->values()
        );

        // Querying everything at once wasn't possble with GraphQL.
        // Trying concurrency with request throttling
        // BEWARE: Not pretty, but effective

        $concurrent = fn (Pool $pool) => $pool->throw()
            ->acceptJson()
            ->withToken($this->config->github_access_token, 'token');

        $responses = Http::pool(fn (Pool $request) => $repositories->map(
            fn ($repo) => $concurrent($request)->async()->get(static::BASE_URL."repos/{$repo}/actions/runs", [
                // Can't filter by multiple statusses. Not with GraphQL either.
                // Need to leave this empty & filter manually. Bummer!!
                // Rather bigger responses than redundant requests.
                'per_page' => 100,
                // 'created' => '>'.now()
                //     ->subMinutes(10)
                //     ->subWeek() // For testing - Comment this out!
                //     ->toIso8601String(),
            ])
        ));

        return collect($responses)
            ->each(
                fn ($response) => ! is_a($response, RequestException::class) ?: logger()->error($response)
            )
            // We don't care about exceptions just yet
            ->filter(
                fn ($response) => is_a($response, Response::class)
            )
            // Unpack & filter only responses with runs
            ->map->json()
            ->where('total_count')
            // Key-by the repository name
            ->mapWithKeys(
                fn ($data, $key) => [$repositories[$key] => $data['workflow_runs']]
            )
            // Filter only running states - Uncomment during development
            ->map(fn ($runs) => array_filter($runs, function ($run) {
                return RunStatus::from($run['status'])->isRunning();
            }))
            ->filter();
    }

    public function workflowRun(string $repo, int $id): Fluent
    {
        logger()->info("Fetching Workflow run: {$id} - {$repo}");

        $response = $this->github
            ->get(static::BASE_URL."repos/{$repo}/actions/runs/{$id}")
            ->json();

        return fluent($response);
    }

    public function restartJobs(string $repo, int $id): void
    {
        logger()->info("Restarting jobs: {$id} - {$repo}");

        $this->github->post(static::BASE_URL."repos/{$repo}/actions/runs/{$id}/rerun", (object) []);
    }

    public function restartFailedJobs(string $repo, int $id): void
    {
        logger()->info("Restarting failed jobs: {$id} - {$repo}");

        $this->github->post(static::BASE_URL."repos/{$repo}/actions/runs/{$id}/rerun-failed-jobs", (object) []);
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
        logger()->info('GitHub User authorized');

        $accessToken = $accessToken
            ? $accessToken
            : $this->config->github_access_token;

        return $this->github
            ->withToken($accessToken)
            ->get(static::BASE_URL.'user')
            ->json();
    }
}
