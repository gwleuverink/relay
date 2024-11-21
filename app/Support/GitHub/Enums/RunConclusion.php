<?php

namespace App\Support\GitHub\Enums;

enum RunConclusion: string
{
    use Concerns\HasHumanReadableValue;

    case ACTION_REQUIRED = 'action_required';
    case CANCELLED = 'cancelled';
    case FAILURE = 'failure';
    case NEUTRAL = 'neutral';
    case SUCCESS = 'success';
    case SKIPPED = 'skipped';
    case STALE = 'stale';
    case TIMED_OUT = 'timed_out';
}
