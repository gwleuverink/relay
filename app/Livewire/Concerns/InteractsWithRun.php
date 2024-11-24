<?php

namespace App\Livewire\Concerns;

use App\Events\WorkflowStatusChanged;
use App\Support\GitHub\Enums\ConclusionStatus;
use App\Support\GitHub\Enums\RunStatus;
use Livewire\Attributes\On;

trait InteractsWithRun
{
    public function restartJobs()
    {
        if (! $this->run->canRestart()) {
            return $this->js("alert('Can\'t restart - Jobs still running')");
        }

        $this->run->update([
            'status' => RunStatus::REQUESTED,
            'conclusion' => null,
        ]);

        $this->github->restartJobs(
            $this->run->repository,
            $this->run->remote_id
        );
    }

    public function restartFailedJobs()
    {
        if (! $this->run->canRestart()) {
            return $this->js("alert('Can\'t restart running Jobs')");
        }

        $this->run->update([
            'status' => RunStatus::REQUESTED,
            'conclusion' => null,
        ]);

        $this->github->restartFailedJobs(
            $this->run->repository,
            $this->run->remote_id
        );
    }

    public function cancelRun()
    {
        if (! $this->run->canCancel()) {
            return $this->js("alert('Can\'t cancel - No running Jobs')");
        }

        $this->run->update([
            'status' => RunStatus::COMPLETED,
            'conclusion' => ConclusionStatus::CANCELLED,
        ]);

        $this->github->cancelRun(
            $this->run->repository,
            $this->run->remote_id
        );
    }

    #[On('native:'.WorkflowStatusChanged::class)]
    public function refreshWhenStatusChanges($id)
    {
        if ($this->run->id !== $id) {
            $this->skipRender();
        }

        $this->run->refresh();
    }
}
