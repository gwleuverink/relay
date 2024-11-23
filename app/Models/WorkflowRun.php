<?php

namespace App\Models;

use App\Events\WorkflowRunDetected;
use App\Events\WorkflowRunPruned;
use App\Events\WorkflowStatusChanged;
use App\Support\GitHub\Enums\ConclusionStatus;
use App\Support\GitHub\Enums\RunStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Fluent;

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
        $startedAt = Carbon::parse($this->data->run_started_at);
        $diff = $startedAt->diffInSeconds() > 60
            ? $startedAt->ago()
            : 'just now';

        return Attribute::make(
            get: fn () => $diff,
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

    /*
    |--------------------------------------------------------------------------
    | Model events
    |--------------------------------------------------------------------------
    */
    protected static function booted(): void
    {
        static::created(function (self $run) {
            WorkflowRunDetected::dispatch($run);
        });

        static::deleted(function (self $run) {
            WorkflowRunPruned::dispatch($run);
        });

        static::updated(function (self $run) {

            if ($run->status->isRunning() !== $run->getOriginal('status')->isRunning()) { // The running status changed
                if ($run->status->isRunning()) { // And is now running
                    WorkflowRunDetected::dispatch($run);
                }
            }

            if ($run->status !== $run->getOriginal('status') || $run->conclusion !== $run->getOriginal('conclusion')) {
                WorkflowStatusChanged::dispatch($run);
            }

        });
    }
}
