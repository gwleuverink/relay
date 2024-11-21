<?php

namespace App\Support\GitHub\Contracts;

use Illuminate\Support\Collection;

interface GitHub
{
    /*
    |--------------------------------------------------------------------------
    | Data fetching
    |--------------------------------------------------------------------------
    */
    public function repos(): Collection;

    public function pendingActions(): array;

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    */
    /** @return array{verification_uri: string, device_code: string, user_code: string} */
    public function startUserVerification(): array;

    public function getAccessToken(string $deviceCode): ?string;

    /** @return array{login: string} */
    public function authorizedUser(string $accessToken): array;
}
