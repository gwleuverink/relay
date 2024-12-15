<?php

namespace App\Providers;

use App\Settings\Config;
use App\Support\GitHub\GitHub;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;
use App\Support\GitHub\Contracts\GitHub as GitHubService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(GitHubService::class, function () {
            return new GitHub(
                Http::createPendingRequest(),
                resolve(Config::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
