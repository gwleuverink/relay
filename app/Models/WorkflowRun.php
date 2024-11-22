<?php

namespace App\Models;

use App\Events\WorkflowRunDetected;
use App\Events\WorkflowRunPruned;
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
        'data',
    ];

    protected $casts = [
        'status' => RunStatus::class,
        'data' => 'object',
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
    | Factory methods
    |--------------------------------------------------------------------------
    */
    public function updateFromRequest(Fluent $run): self
    {
        $this->update([
            'status' => $run->status,
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
                'repository' => $repository,
                'status' => $run->status,
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

            if ($run->status->isRunning() === $run->getOriginal('status')->isRunning()) {
                // logger('not changed: '.$run->name, [$run->status->value, $run->getOriginal('status')->value]);

                return; // Status didn't change
            }

            if ($run->status->isRunning()) {
                // logger('changed: '.$run->name, [$run->status->value, $run->getOriginal('status')->value]);
                WorkflowRunDetected::dispatch($run);
            }
        });
    }
}
