<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->encrypt('config.github_access_token');
        $this->migrator->encrypt('config.github_username');
    }
};
