<?php

namespace App\Models;

use Carbon\CarbonInterface;
use App\Casts\JobCollection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Fluent;
use App\Observers\WorkflowRunObserver;
use App\Support\GitHub\Enums\RunStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Support\GitHub\Enums\ConclusionStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

/**
 * @property CarbonInterface $started_at
 */
#[ObservedBy([WorkflowRunObserver::class])]
class WorkflowRun extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'id',
        'remote_id',
        'repository',
        'name',
        'status',
        'conclusion',
        'data',
        'jobs',
    ];

    protected $casts = [
        'data' => 'object',
        'jobs' => JobCollection::class,

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

    public function canDelete(): bool
    {
        return $this->status === RunStatus::COMPLETED && $this->conclusion === ConclusionStatus::FAILURE;
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
            'status' => $run->status, // @phpstan-ignore property.notFound
            'conclusion' => $run->conclusion, // @phpstan-ignore property.notFound
            'data' => $run->toArray(),
        ]);

        return $this;
    }

    public static function updateOrCreateFromRequest(string $repository, Fluent $run): self
    {
        return static::updateOrCreate(
            [
                'remote_id' => $run->id, // @phpstan-ignore property.notFound
            ],
            [
                'repository' => $repository,
                'conclusion' => $run->status, // @phpstan-ignore property.notFound
                'status' => $run->status, // @phpstan-ignore property.notFound
                'name' => $run->name, // @phpstan-ignore property.notFound
                'data' => $run,
            ]
        );
    }
}
