<div
    wire:init="startUserVerification"
    wire:poll.keep-alive.8s="pollAuthorization"
    class="p-4"
>
    <p>By authenticating with GitHub, you can see workflows of private repos and enjoy higher rate limits.</p>

    <p>
        You will need to type this code when connecting to GitHub:
        <span>
            @if ($userCode && $deviceCode)
                <span class="select-all bg-blue-800 px-3 py-2 text-white">{{ $userCode }}</span>
            @else
                loading...
            @endif
        </span>
    </p>

    <a
        href="https://github.com/login/device"
        target="_blank"
        class="inline-block"
    >
        Connect to GitHub.
    </a>

    <p>
        Copy this URL in your browser, if you can't click the link above:
        <span class="select-all text-indigo-700 underline underline-offset-2">https://github.com/login/device</span>
    </p>
</div>
