<?php

namespace App\Support\GitHub\Contracts;

use Illuminate\Support\Fluent;
use Illuminate\Support\Collection;

interface GitHub
{
    /*
    |--------------------------------------------------------------------------
    | Data fetching
    |--------------------------------------------------------------------------
    */
    public function repos(int $take = 50): Collection;

    public function runningWorkflows(array $repositories): Collection;

    public function workflowRun(string $repo, int $id): Fluent;

    public function workflowJobs(string $repo, int $id): Collection;

    public function cancelRun(string $repo, int $id): void;

    public function restartJobs(string $repo, int $id): void;

    public function restartFailedJobs(string $repo, int $id): void;

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    */
    /** @return array{verification_uri: string, device_code: string, user_code: string, expires_in: int} */
    public function startUserVerification(): array;

    public function getAccessToken(string $deviceCode): ?string;

    /** @return array{login: string} */
    public function authorizedUser(string $accessToken): array;
}
