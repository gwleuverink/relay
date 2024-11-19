<div
    wire:init="startUserVerification"
    wire:poll.keep-alive.8s="pollAuthorization"
    class="flex min-h-screen flex-col p-4"
>
    <div class="flex w-full flex-col items-center space-y-6 px-4 py-6">
        <x-svg.github-mark />

        <x-input.button
            :href="$verificationUri"
            x-default-browser
            target="_blank"
        >
            Connect with GitHub
        </x-input.button>
    </div>

    <div>
        <p>Please connect with GitHub to continue.</p>

        <p>You will need to type this code prompted:</p>
    </div>

    <div class="mt-2">
        @if ($userCode && $deviceCode)
            <code class="inline-block cursor-default select-all rounded-sm bg-blue-800 px-2 py-0.5 font-mono text-xs font-semibold text-white">{{ $userCode }}</code>
        @else
            <span>loading...</span>
        @endif
    </div>

    <div class="mt-auto text-xs">
        <p>
            Copy this URL in your browser if you can't click the link above:
            <span class="inline cursor-default select-all text-indigo-700 underline underline-offset-2">
                {{ $verificationUri }}
            </span>
        </p>
    </div>
</div>
