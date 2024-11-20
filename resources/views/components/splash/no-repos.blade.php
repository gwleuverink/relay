<div class="flex flex-col justify-center p-4 pt-24 text-center text-lg text-neutral-400">
    <p>Not watching any repositories.</p>

    <p>
        Click
        <a
            wire:navigate.hover
            href="{{ route('settings') }}"
            class="inline-flex translate-y-0.5 cursor-default items-center text-neutral-400 transition-colors hover:text-neutral-500"
        >
            <x-heroicon-c-cog-6-tooth class="w-4" />
        </a>
        To configure your repo's
    </p>
</div>
