<?php

namespace App\Models;

use App\Observers\WorkflowRunObserver;
use App\Support\GitHub\Enums\ConclusionStatus;
use App\Support\GitHub\Enums\RunStatus;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Fluent;

#[ObservedBy([WorkflowRunObserver::class])]
class WorkflowRun extends Model
{
    protected $fillable = [
        'id',
        'remote_id',
        'repository',
        'name',
        'status',
        'conclusion',
        'data',
    ];

    protected $casts = [
        'data' => 'object',
        'status' => RunStatus::class,
        'conclusion' => ConclusionStatus::class,
    ];

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */
    public function startedAtDiff(): Attribute
    {
        $diff = $this->started_at->diffInSeconds() > 60
            ? $this->started_at->ago()
            : 'just now';

        return Attribute::make(
            get: fn () => $diff,
        );
    }

    public function startedAt(): Attribute
    {
        return Attribute::make(
            get: fn () => Carbon::parse($this->data->run_started_at),
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Support
    |--------------------------------------------------------------------------
    */
    public function canRestart(): bool
    {
        return in_array($this->conclusion, [
            ConclusionStatus::STALE,
            ConclusionStatus::FAILURE,
            ConclusionStatus::CANCELLED,
            ConclusionStatus::TIMED_OUT,
        ]);
    }

    public function canCancel(): bool
    {
        return in_array($this->status, [
            RunStatus::IN_PROGRESS,
        ]);
    }

    public function sortWeight(): int
    {
        $statusWeight = match ($this->status) {
            RunStatus::REQUESTED => 1,
            RunStatus::QUEUED => 2,
            RunStatus::PENDING => 3,
            RunStatus::IN_PROGRESS => 4,
            default => 999,
        };

        $compoundWeight = match ($this->conclusion) {
            ConclusionStatus::FAILURE => 5,
            ConclusionStatus::CANCELLED => 6,
            ConclusionStatus::TIMED_OUT => 7,
            ConclusionStatus::SKIPPED => 8,
            ConclusionStatus::SUCCESS => 9,
            default => $statusWeight
        };

        return $compoundWeight;
    }

    /*
    |--------------------------------------------------------------------------
    | Factory methods
    |--------------------------------------------------------------------------
    */
    public function updateFromRequest(Fluent $run): self
    {
        $this->update([
            'status' => $run->status,
            'conclusion' => $run->conclusion,
            'data' => $run->toArray(),
        ]);

        return $this;
    }

    public static function updateOrCreateFromRequest(string $repository, Fluent $run): self
    {
        return static::updateOrCreate(
            [
                'remote_id' => $run->id,
            ],
            [
                'status' => $run->status,
                'conclusion' => $run->status,
                'repository' => $repository,
                'name' => $run->name,
                'data' => $run,
            ]
        );
    }
}
