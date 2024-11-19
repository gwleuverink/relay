<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class Config extends Settings
{
    public readonly string $github_client_id;

    public string $github_access_token;

    public string $github_username;

    public function __construct()
    {
        $this->github_client_id = config('services.github.client_id');
    }

    public static function group(): string
    {
        return 'config';
    }
}
