<?php

namespace Tests;

use App\Settings\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Http::fake();
        $this->withoutVite();
    }

    public function getRoute($name, array $parameters = [], array $headers = [])
    {
        return $this->get(route($name, $parameters), $headers);
    }

    public function login()
    {
        resolve(Config::class)->fill([
            'github_access_token' => 1234,
            'github_username' => 'gwleuverink',
        ])->save();
    }
}
