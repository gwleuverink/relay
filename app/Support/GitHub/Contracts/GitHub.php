<?php

namespace App\Support\GitHub\Contracts;

interface GitHub
{
    /** @return array{verification_uri: string, device_code: string, user_code: string} */
    public function startUserVerification(): array;

    public function getAccessToken(string $deviceCode): ?string;

    /** @return array{login: string} */
    public function getAuthorizedUser(string $accessToken): array;
}
