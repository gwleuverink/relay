<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class Config extends Settings
{
    // public readonly string $github_client_id;

    public string $github_access_token;

    public string $github_username;

    public static function group(): string
    {
        return 'config';
    }
}
