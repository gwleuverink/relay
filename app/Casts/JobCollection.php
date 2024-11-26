<?php

namespace App\Casts;

use App\Support\GitHub\Enums\RunStatus;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class JobCollection implements CastsAttributes
{

    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return collect(json_decode($value))->map(function($job) {
            $job = $this->castProperties($job);
            $job->steps = collect($job->steps)->map(
                fn($step) => $this->castProperties($step)
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
        $data->conclusion = RunStatus::tryFrom($data->conclusion);

        return $data;
    }
}
