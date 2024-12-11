<?php

use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\FetchWorkflowRuns;

Schedule::command(FetchWorkflowRuns::class)
    ->everyFifteenSeconds()
    ->runInBackground();
