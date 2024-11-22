<?php

namespace App\Support\GitHub\Enums;

enum ConclusionStatus: string
{
    use Concerns\HasHumanReadableValue;

    case SUCCESS = 'success';
    case FAILURE = 'failure';
    case CANCELLED = 'cancelled';
    case NEUTRAL = 'neutral';
    case SKIPPED = 'skipped';
    case TIMED_OUT = 'timed_out';
    case ACTION_REQUIRED = 'action_required';
    case STALE = 'stale';
    case WAITING = 'waiting';

    case REQUESTED = 'requested';
    case QUEUED = 'queued';
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
}
