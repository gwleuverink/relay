<?php

namespace App\Support\GitHub\Contracts;

interface GitHub
{
    /*
    |--------------------------------------------------------------------------
    | Data fetching
    |--------------------------------------------------------------------------
    */
    public function repos(): array;

    public function actions(): array;

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
