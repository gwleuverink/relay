<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class Config extends Settings
{
    public ?string $github_username;
    public ?string $github_access_token;
    public bool $github_poll_by_recent_push = true;
    public array $github_selected_repositories = [];

    public static function group(): string
    {
        return 'config';
    }

    public static function encrypted(): array
    {
        return [
            'github_username',
            'github_access_token',
        ];
    }
}
