<?php

namespace Database\Factories;

use App\Support\GitHub\Enums\RunStatus;
use App\Support\GitHub\Enums\ConclusionStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkflowRunFactory extends Factory
{
    public function definition(): array
    {
        return [
            'remote_id' => fake()->randomNumber(),
            'repository' => fake()->word() . '/' . fake()->word(),
            'name' => fake()->words(2, true),
            'status' => RunStatus::REQUESTED,
            'conclusion' => ConclusionStatus::NEUTRAL,
            'data' => $this->dataStub(),
            'jobs' => $this->jobsStub(),
        ];
    }

    public function status(RunStatus $status): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => $status,
        ]);
    }

    public function conclusion(ConclusionStatus $status): static
    {
        return $this->state(fn (array $attributes) => [
            'conclusion' => $status,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Stubs
    |--------------------------------------------------------------------------
    */
    private function dataStub(): array
    {
        return [
            'name' => fake()->word(),
            'event' => fake()->word(),
            'run_started_at' => now(),
            'html_url' => fake()->url(),
            'head_branch' => fake()->word(),
            'head_sha' => fake()->randomNumber(),
            'run_number' => fake()->randomNumber(),
            'display_title' => fake()->words(2, true),
            'repository' => [
                'full_name' => fake()->word() . '/' . fake()->word(),
                'head_branch' => fake()->word(),
                'html_url' => fake()->url(),
            ],
            'triggering_actor' => [
                'login' => fake()->word(),
                'html_url' => fake()->url(),
                'avatar_url' => fake()->imageUrl(),
            ],
            'head_commit' => [
                'id' => fake()->randomNumber(),
                'message' => fake()->sentence(),
            ],
        ];
    }

    private function jobsStub(): array
    {
        return [];
    }
}
