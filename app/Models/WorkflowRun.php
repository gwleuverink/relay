<?php

namespace App\Models;

use App\Events\WorkflowRunDetected;
use App\Support\GitHub\Enums\RunStatus;
use Illuminate\Database\Eloquent\Model;

class WorkflowRun extends Model
{
    protected $fillable = [
        'id',
        'remote_id',
        'repository',
        'status',
        'data',
    ];

    protected $casts = [
        'status' => RunStatus::class,
        'data' => 'array',
    ];

    public static function updateOrCreateFromRequest(string $repository, array $run): self
    {
        $run = fluent($run);

        return static::updateOrCreate(
            [
                'remote_id' => $run->id,
                'repository' => $repository,
            ],
            [
                'status' => $run->status,
                'data' => $run,
            ]
        );
    }

    protected static function booted(): void
    {
        static::created(function (self $run) {
            WorkflowRunDetected::dispatch($run);
        });
    }
}
