<?php

namespace App\Support\GitHub;

use Exception;
use App\Settings\Config;
use GuzzleHttp\Promise\Each;
use Illuminate\Support\Fluent;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Collection;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use App\Support\GitHub\Enums\RunStatus;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use App\Support\GitHub\Contracts\GitHub as Service;

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

    /** @return Collection<array> [[id: string, name: string, full_name: string]] */
    public function repos(int $take = 50): Collection
    {
        logger()->info('Fetching repositories...');

        // TODO: Consider filtering pushed_at date with max age
        $response = $this->github->post(static::BASE_URL . 'graphql', [
            'query' => file_get_contents(__DIR__ . '/Queries/repositories.graphql'),
            'variables' => [
                'take' => $take,
            ],
        ])->json();

        if (array_key_exists('errors', $response ?? [])) {
            throw new Exception('An error occured fetching data from GitHub');
        }

        $userRepos = data_get($response, 'data.viewer.repositories.nodes');
        $orgRepos = data_get($response, 'data.viewer.organizations.nodes.*.repositories.nodes.*');

        return collect($userRepos)
            ->merge($orgRepos)
            ->unique('nameWithOwner')
            ->sortByDesc('pushedAt');
    }

    public function runningWorkflows(array $repositories): Collection
    {
        logger()->info('Fetching Workflow runs...', [
            'repos' => count($repositories),
        ]);

        // Querying everything at once isn't possble with GraphQL. We need to make a significant amount of requests.
        // Making a lot concurrently tiggers GitHub's rate limiter (even if we're well within our request limit)
        // We're going to use concurrency with request batching to control the rate of requests going out.
        //
        // BEWARE: Not pretty, but effective

        // First we setup a function that yields the requests
        $concurrent = function (Pool $pool) use ($repositories) {
            foreach ($repositories as $repo) {
                yield $pool->withToken($this->config->github_access_token, 'token')
                    ->acceptJson()
                    ->async()
                    ->throw()
                    ->get(static::BASE_URL . "repos/{$repo}/actions/runs", [
                        'per_page' => 50,
                    ]);
            }
        };

        // Then we pool the responses in batches using Each::ofLimit
        $responses = Http::pool(fn (Pool $request) => [
            Each::ofLimit(
                $concurrent($request),
                10 // Batches concurrent requests
            )->wait(),
        ]);

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
            // Key-by the repository name & map only the runs
            ->mapWithKeys(function ($data) {
                // Note we can't simply fetch the repo name by key from the
                // $repositories variable. The order depends on the order
                // of repsopnses that come back. The keys can differ.
                $runs = $data['workflow_runs'];
                $repo = data_get(head($runs), 'repository.full_name');

                return [$repo => $runs];
            })
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
            ->get(static::BASE_URL . "repos/{$repo}/actions/runs/{$id}")
            ->json();

        return fluent($response);
    }

    public function workflowJobs(string $repo, int $id): Collection
    {
        logger()->info("Fetching Workflow jobs: {$id} - {$repo}");

        return $this->github
            ->get(static::BASE_URL . "repos/{$repo}/actions/runs/{$id}/jobs")
            ->collect();
    }

    public function cancelRun(string $repo, int $id): void
    {
        logger()->info("Cancelling run: {$id} - {$repo}");

        /* @phpstan-ignore argument.type */ /* Must explicitly pass object|null or will throw Http error */
        $this->github->post(static::BASE_URL . "repos/{$repo}/actions/runs/{$id}/cancel", (object) []);
    }

    public function restartJobs(string $repo, int $id): void
    {
        logger()->info("Restarting jobs: {$id} - {$repo}");

        /* @phpstan-ignore argument.type */ /* Must explicitly pass object|null or will throw Http error */
        $this->github->post(static::BASE_URL . "repos/{$repo}/actions/runs/{$id}/rerun", (object) []);
    }

    public function restartFailedJobs(string $repo, int $id): void
    {
        logger()->info("Restarting failed jobs: {$id} - {$repo}");

        /* @phpstan-ignore argument.type */ /* Must explicitly pass object|null or will throw Http error */
        $this->github->post(static::BASE_URL . "repos/{$repo}/actions/runs/{$id}/rerun-failed-jobs", (object) []);
    }

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    */

    /** @return array{verification_uri: string, device_code: string, user_code: string, expires_in: int} */
    public function startUserVerification(): array
    {
        return $this->github
            ->post(static::AUTH_URL . 'device/code', [
                'client_id' => config('services.github.client_id'),
                'scope' => implode(', ', static::SCOPES),
            ])->json();
    }

    public function getAccessToken(string $deviceCode): ?string
    {
        $response = $this->github
            ->post(static::AUTH_URL . '/oauth/access_token', [
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
        logger()->info('Fetching authenticated user');

        $accessToken = $accessToken
            ? $accessToken
            : $this->config->github_access_token;

        return $this->github
            ->withToken($accessToken)
            ->get(static::BASE_URL . 'user')
            ->json();
    }
}
