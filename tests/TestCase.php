<?php

namespace Tests;

use App\Settings\Config;
use Illuminate\Support\Facades\Http;
use App\Support\GitHub\Contracts\GitHub;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Http::fake();
        $this->mockRepos();
        $this->withoutVite();
    }

    protected function getRoute($name, array $parameters = [], array $headers = [])
    {
        return $this->get(route($name, $parameters), $headers);
    }

    protected function login()
    {
        resolve(Config::class)->fill([
            'github_access_token' => 1234,
            'github_username' => 'gwleuverink',
        ])->save();
    }

    protected function mockRepos(array $repos = [])
    {
        $response = collect($repos)->map(fn ($repo) => [
            'nameWithOwner' => $repo,
            'owner' => [
                '__typename' => 'User',
                'avatarUrl' => fake()->imageUrl(),
            ],
        ]);

        $this->mock(GitHub::class)
            ->shouldReceive('repos')
            ->andReturn($response);
    }
}
