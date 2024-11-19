<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('config.github_access_oken', '');
        $this->migrator->add('config.github_username', '');
    }
};
