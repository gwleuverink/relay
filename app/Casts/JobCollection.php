<?php

namespace App\Casts;

use Illuminate\Support\Carbon;
use App\Support\GitHub\Enums\RunStatus;
use Illuminate\Database\Eloquent\Model;
use App\Support\GitHub\Enums\ConclusionStatus;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class JobCollection implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return collect(json_decode($value))->map(function ($job) {
            $job = $this->castProperties($job);
            $job->steps = collect($job->steps)->map(
                fn ($step) => $this->castProperties($step)
            )->toArray();

            return $job;
        });
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return json_encode($value);
    }

    private function castProperties(object $data): object
    {
        $data->status = RunStatus::tryFrom($data->status);
        $data->conclusion = ConclusionStatus::tryFrom($data->conclusion);

        $data->started_at = Carbon::parse($data->started_at);
        $data->completed_at = Carbon::parse($data->completed_at);

        return $data;
    }
}
