<?php

use App\Models\WorkflowRun;
use App\Livewire\WorkflowRun\DetailWindow;

it('can open detail window', function () {
    $run = WorkflowRun::factory()->create();

    $this->livewire(DetailWindow::class, ['run' => $run])
        ->assertSuccessful();
});

it('can rerun when authorized')->todo();
it('can cancel when authorized')->todo();
it('can delete when autorized')->todo();
