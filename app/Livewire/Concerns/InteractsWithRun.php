<?php

namespace App\Livewire\Concerns;

use Livewire\Attributes\On;
use App\Events\WorkflowStatusChanged;
use App\Support\GitHub\Enums\RunStatus;
use App\Support\GitHub\Enums\ConclusionStatus;

trait InteractsWithRun
{
    public function restartJobs()
    {
        if (! $this->run->canRestart()) {
            return $this->js("alert('Cannot restart a check suite that is running.')");
        }

        $this->run->update([
            'status' => RunStatus::REQUESTED,
            'conclusion' => null,
            'jobs' => null,
        ]);

        $this->github->restartJobs(
            $this->run->repository,
            $this->run->remote_id
        );
    }

    public function restartFailedJobs()
    {
        if (! $this->run->canRestart()) {
            return $this->js("alert('Cannot restart a check suite that is running.')");
        }

        $this->run->update([
            'status' => RunStatus::REQUESTED,
            'conclusion' => null,
            'jobs' => null,
        ]);

        $this->github->restartFailedJobs(
            $this->run->repository,
            $this->run->remote_id
        );
    }

    public function cancelRun()
    {
        if (! $this->run->canCancel()) {
            return $this->js("alert('Cannot cancel a check suite that is completed.')");
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

    public function deleteRun()
    {
        if (! $this->run->canDelete()) {
            return $this->js("alert('Cannot stop tracking a check suite that is running.')");
        }

        // This soft deletes & gets cleaned by the scheduler
        // so we can gracefully close run detail window
        $this->run->delete();
    }

    #[On('native:' . WorkflowStatusChanged::class)]
    public function refreshWhenStatusChanges($id)
    {
        if ($this->run->id !== $id) {
            $this->skipRender();
        }

        $this->run->refresh();
    }
}
