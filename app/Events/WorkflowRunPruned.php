<?php

namespace App\Events;

use App\Models\WorkflowRun;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

class WorkflowRunPruned implements ShouldBroadcastNow
{
    use Dispatchable;

    public function __construct(WorkflowRun $run)
    {
        //
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('nativephp'),
        ];
    }
}
