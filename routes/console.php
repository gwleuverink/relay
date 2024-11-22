<?php

use App\Jobs\FetchWorkflowRuns;
use Illuminate\Support\Facades\Schedule;

Schedule::call(function () {
    FetchWorkflowRuns::dispatchSync();
})->everyTenSeconds();
