<?php

namespace App\Events;

use App\Models\WorkflowRun;
use Illuminate\Broadcasting\Channel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class WorkflowStatusChanged implements ShouldBroadcastNow
{
    use Dispatchable;

    public int $id;

    public function __construct(WorkflowRun $run)
    {
        $this->id = $run->id;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('nativephp'),
        ];
    }
}
