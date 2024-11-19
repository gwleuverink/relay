<?php

namespace App\Http\Middleware;

use App\Settings\Config;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Authenticated
{
    public function __construct(protected Config $config) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->config->github_username || ! $this->config->github_access_token) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
