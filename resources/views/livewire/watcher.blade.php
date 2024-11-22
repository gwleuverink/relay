<div class="relative min-h-screen bg-neutral-100 text-xs">
    <div class="fixed left-0 right-0 top-0 z-10 flex justify-end border-b border-neutral-200 bg-gradient-to-r from-neutral-100 to-neutral-200 font-semibold text-neutral-700">
        <a
            wire:navigate.hover
            href="{{ route('settings') }}"
            class="cursor-default p-2 text-neutral-400 transition-colors hover:text-neutral-500"
        >
            <x-heroicon-c-cog-6-tooth class="w-3.5" />
        </a>
    </div>

    {{-- Empty & no repo's configured --}}
    {{-- <x-splash.no-repos /> --}}

    {{-- Empty & repo's configured --}}
    {{-- <x-splash.no-actions /> --}}

    <div class="divide-y divide-neutral-200 shadow-md">
        @foreach ($this->runs as $run)
            <x-action.group
                :type="$run->status"
                :repo="$run->repository"
                trigger="PR #342"
                triggered-at="2m ago"
            >
                <x-action.job
                    status="queued"
                    name="Integration Tests"
                    environment="ubuntu-latest • Node 18"
                />
            </x-action.group>
        @endforeach

        {{--
            <x-action.group
            type="running"
            repo="gwleuverink/bundle"
            trigger="PR #342"
            triggered-at="2m ago"
            >
            <x-action.job
            status="queued"
            name="Unit Tests"
            environment="ubuntu-latest • Node 16"
            />
            <x-action.job
            status="queued"
            name="Integration Tests"
            environment="ubuntu-latest • Node 18"
            />
            <x-action.job
            status="finished"
            name="Lint Check"
            environment="ubuntu-latest • Node 16"
            />
            </x-action.group>
            
            <x-action.group
            type="idle"
            repo="media-code/WCK-wijck.com"
            trigger="Merge to main"
            triggered-at="1h ago"
            >
            <x-action.job
            status="queued"
            name="Unit Tests"
            environment="ubuntu-latest • Node 16"
            />
            </x-action.group>
        --}}
    </div>
</div>
