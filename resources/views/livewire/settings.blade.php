<div class="relative pb-4 pt-12 text-xs">
    <div class="fixed left-0 right-0 top-0 z-10 flex justify-start border-b border-neutral-200 bg-gradient-to-r from-neutral-100 to-neutral-200 font-semibold text-neutral-700">
        <a
            wire:navigate.hover
            href="{{ route('watcher') }}"
            class="cursor-default p-2 text-neutral-400 transition-colors hover:text-neutral-500"
        >
            <x-heroicon-c-arrow-left class="w-3.5" />
        </a>
    </div>

    <div class="px-4">
        {{-- Selected repos's --}}
        <div class="flex flex-col">
            @foreach ($selectedRepositories as $repo)
                <x-input.checkbox
                    wire:model.live="selectedRepositories"
                    :wire:key="'repo-'.$repo"
                    :value="$repo"
                    :label="$repo"
                />
            @endforeach

            @unless (empty($selectedRepositories))
                <hr class="my-4" />
            @endunless
        </div>

        {{-- Selectable repos's --}}
        <div class="flex flex-col space-y-1">
            @foreach ($this->repositories() as $repo)
                <x-input.checkbox
                    wire:model.live="selectedRepositories"
                    :wire:key="'repo-'.$repo['nameWithOwner']"
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
</div>
