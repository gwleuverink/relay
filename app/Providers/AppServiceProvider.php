<?php

namespace App\Providers;

use App\Settings\Config;
use App\Support\GitHub\Contracts\GitHub as GitHubService;
use App\Support\GitHub\GitHub;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(GitHubService::class, function () {
            return new GitHub(
                resolve(PendingRequest::class),
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
