<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Artisan;
use App\Console\Commands\FetchWorkflowRuns;
use Native\Laravel\Events\App\ApplicationBooted;

class ApplicationBootedListener
{
    public function handle(ApplicationBooted $event): void
    {
        Artisan::call(FetchWorkflowRuns::class);
    }
}
