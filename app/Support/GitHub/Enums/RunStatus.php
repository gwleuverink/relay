<?php

namespace App\Support\GitHub\Enums;

enum RunStatus: string
{
    use Concerns\HasHumanReadableValue;

    // Status
    case IN_PROGRESS = 'in_progress';
    case QUEUED = 'queued';
    case PENDING = 'pending';
    case REQUESTED = 'requested';

    // Completion
    case SUCCESS = 'success';
    case FAILURE = 'failure';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case NEUTRAL = 'neutral';
    case SKIPPED = 'skipped';
    case TIMED_OUT = 'timed_out';
    case ACTION_REQUIRED = 'action_required';
    case STALE = 'stale';
    case WAITING = 'waiting';

    public function isRunning(): bool
    {
        return match ($this) {
            self::IN_PROGRESS, self::QUEUED, self::PENDING, self::REQUESTED => true,
            default => false
        };
    }

    public function isFinished(): bool
    {
        return ! $this->isRunning();
    }

    public function isCompleted(): bool
    {
        return match ($this) {
            self::COMPLETED, self::SKIPPED => true,
            default => false
        };
    }

    public static function running(): array
    {
        return collect(self::cases())
            ->filter->isRunning()
            ->map->value
            ->toArray();
    }
}
