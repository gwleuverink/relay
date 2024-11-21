<?php

namespace App\Support\GitHub\Enums;

enum RunStatus: string
{
    use Concerns\HasHumanReadableValue;

    case QUEUED = 'queued';
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
}
