<?php

namespace App\Events;

use App\Models\WorkflowRun;
use Illuminate\Broadcasting\Channel;
use Native\Laravel\Facades\Notification;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class WorkflowRunDetected implements ShouldBroadcastNow
{
    use Dispatchable;

    public function __construct(WorkflowRun $run)
    {
        Notification::title('Workflow run queued')
            ->message($run->repository)
            ->show();
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('nativephp'),
        ];
    }
}
