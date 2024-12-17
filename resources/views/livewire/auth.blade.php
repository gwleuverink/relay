<div
    wire:init="startUserVerification"
    wire:poll.keep-alive.8s="pollAuthorization"
    class="flex min-h-screen flex-col bg-gradient-to-br from-white to-neutral-100 px-4 pb-5 pt-9"
>
    {{-- Header --}}
    <div class="flex flex-col items-center space-y-2">
        <x-svg.github-mark class="size-12" />
        <h1 class="text-center text-lg">Connect to GitHub</h1>
    </div>

    {{-- Code copy button --}}
    <div class="mt-6">
        <button
            type="button"
            title="Copy code"
            :disabled="copied"
            x-data="{ copied: false }"
            x-on:click="
                () => {
                    navigator.clipboard.writeText('{{ $userCode }}')
                    copied = true
                    setTimeout(() => (copied = false), 1000)
                }
            "
            class="group flex w-full items-center rounded-lg bg-gray-200 p-4 text-neutral-700"
        >
            @if ($userCode && $deviceCode)
                <code class="select-noneß font-mono text-lg font-semibold">{{ $userCode }}</code>
            @else
                <span class="text-lg">loading...</span>
            @endif

            <div
                x-cloak
                class="ml-auto"
            >
                <x-heroicon-m-clipboard-document
                    x-show="copied === false"
                    class="size-4 text-neutral-500 opacity-50 transition-opacity group-hover:opacity-80"
                />

                <x-heroicon-m-check
                    x-show="copied === true"
                    class="size-4 text-neutral-500 opacity-80"
                />
            </div>
        </button>
    </div>

    {{-- Instructions --}}
    <div class="mt-8 text-sm text-gray-600">
        <p class="text-center">Visit GitHub and enter the code to connect</p>
        <a
            x-open-external
            href="{{ $verificationUri }}"
            target="_blank"
            rel="noopener noreferrer"
            class="mt-2 flex items-center justify-center space-x-2 font-medium text-blue-600 hover:text-blue-800"
        >
            <span>github.com/login/device</span>
            <x-heroicon-c-link class="size-4" />
        </a>
    </div>

    {{-- Loading indicator --}}
    <div class="mt-auto flex items-center justify-center space-x-2 text-sm text-gray-500">
        <div class="h-2 w-2 animate-pulse rounded-full bg-blue-600"></div>
        <span>Waiting for authentication...</span>
    </div>
</div>
