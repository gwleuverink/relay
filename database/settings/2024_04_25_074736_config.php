<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('config.github_access_token', null);
        $this->migrator->add('config.github_username', null);
    }
};
