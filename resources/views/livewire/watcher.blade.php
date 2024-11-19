<div class="bg-neutral-100 text-xs shadow-md">
    <div class="border-b border-neutral-200 bg-gradient-to-r from-neutral-100 to-neutral-200 p-2 font-semibold text-neutral-700">GitHub Actions</div>

    <div class="divide-y divide-neutral-200">
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
    </div>
</div>
