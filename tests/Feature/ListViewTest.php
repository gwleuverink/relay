<?php

use App\Models\WorkflowRun;
use App\Livewire\WorkflowRun\ListView;
use App\Support\GitHub\Enums\RunStatus;
use App\Support\GitHub\Enums\ConclusionStatus;

use function Pest\Livewire\livewire;

beforeEach()->fakeHttp();

it('displays message when nothing is running')
    ->livewire(ListView::class)
    ->assertSee('No running Workflows');

it('sorts by compound run and completion status', function () {

    $queued = WorkflowRun::factory()->status(RunStatus::QUEUED)->create();
    $pending = WorkflowRun::factory()->status(RunStatus::PENDING)->create();
    $requested = WorkflowRun::factory()->status(RunStatus::REQUESTED)->create();
    $inProgress = WorkflowRun::factory()->status(RunStatus::IN_PROGRESS)->create();

    $success = WorkflowRun::factory()->conclusion(ConclusionStatus::SUCCESS)->create();
    $skipped = WorkflowRun::factory()->conclusion(ConclusionStatus::SKIPPED)->create();
    $failure = WorkflowRun::factory()->conclusion(ConclusionStatus::FAILURE)->create();
    $timedOut = WorkflowRun::factory()->conclusion(ConclusionStatus::TIMED_OUT)->create();
    $cancelled = WorkflowRun::factory()->conclusion(ConclusionStatus::CANCELLED)->create();

    livewire(ListView::class)->assertSeeInOrder([
        // RunStatus
        $requested->name,
        $queued->name,
        $pending->name,
        $inProgress->name,
        // ConclusionStatus
        $failure->name,
        $timedOut->name,
        $skipped->name,
        $success->name,
        $cancelled->name,
    ]);
});
