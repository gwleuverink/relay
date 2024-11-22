<?php

namespace App\Support\GitHub\Enums;

enum RunStatus: string
{
    use Concerns\HasHumanReadableValue;

    case REQUESTED = 'requested';
    case QUEUED = 'queued';
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';

    public function isRunning(): bool
    {
        return ! $this->isFinished();
    }

    public function isFinished(): bool
    {
        return $this === self::COMPLETED;
    }
}
