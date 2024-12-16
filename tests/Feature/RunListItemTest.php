<?php

use App\Models\WorkflowRun;
use App\Livewire\WorkflowRun\ListItem;

it('displays workflow run', function () {
    $run = WorkflowRun::factory()->create();

    $this->livewire(ListItem::class, ['run' => $run])
        ->assertSuccessful();
});

it('can restart from context menu when authorized')->todo();
it('can cancel from context menu when authorized')->todo();
it('can delete from context menu when authorized')->todo();
