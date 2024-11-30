<x-layouts.window>
    <x-slot name="header">
        <h1 class="sr-only">Workflow Monitor Settings</h1>

        <a
            wire:navigate.hover
            href="{{ route('watcher') }}"
            class="cursor-default rounded-full p-1 text-neutral-400 ring-indigo-200 transition-colors hover:text-neutral-500 focus:outline-none focus:ring-2"
        >
            <x-heroicon-c-arrow-left class="w-3.5" />
        </a>
    </x-slot>

    <div class="p-4">
        {{-- All repo's input --}}
        <div class="flex flex-col">
            <x-input.checkbox
                :disabled="empty($selectedRepositories)"
                wire:model.live="all"
                wire:key="repo-all"
                value="true"
            >
                <x-slot:label class="flex items-center space-x-2">
                    <x-heroicon-m-list-bullet class="w-5 grow-0" />

                    <span>Poll by most recent push</span>
                </x-slot>
            </x-input.checkbox>

            {{-- Selected repos's --}}
            @foreach ($selectedRepositories as $repo)
                <x-input.checkbox
                    wire:model.live="selectedRepositories"
                    :wire:key="'repo-' . $repo"
                    :value="$repo"
                    :label="$repo"
                />
            @endforeach
        </div>

        <hr class="my-4" />

        {{-- Selectable repos's --}}
        <div class="flex flex-col space-y-1">
            @foreach ($this->repositories() as $repo)
                <x-input.checkbox
                    wire:model.live="selectedRepositories"
                    :wire:key="'repo-' . $repo['nameWithOwner']"
                    :value="$repo['nameWithOwner']"
                    :disabled="count($selectedRepositories) >= static::MAX_REPOSITORIES"
                >
                    <x-slot:label class="flex items-center space-x-2">
                        <img
                            src="{{ $repo['owner']['avatarUrl'] }}"
                            alt="Organization Avatar"
                            @class([
                                'w-5 grow-0 border shadow',
                                'rounded-full' => $repo['owner']['__typename'] === 'User',
                                'rounded' => $repo['owner']['__typename'] === 'Organization',
                            ])
                        />

                        <span>{{ $repo['nameWithOwner'] }}</span>
                    </x-slot>
                </x-input.checkbox>
            @endforeach
        </div>
    </div>
</x-layouts.window>
