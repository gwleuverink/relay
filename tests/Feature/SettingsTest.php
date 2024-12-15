<?php

use App\Support\GitHub\Contracts\GitHub;

beforeEach()->login();

beforeEach(fn () => $this->mock(GitHub::class)->shouldReceive('repos')->andReturn(collect([
    [
        'nameWithOwner' => 'foo/bar',
        'owner' => [
            '__typename' => 'User',
            'avatarUrl' => fake()->imageUrl(),
        ],
    ],
    [
        'nameWithOwner' => 'baz/zah',
        'owner' => [
            '__typename' => 'User',
            'avatarUrl' => fake()->imageUrl(),
        ],
    ],
])));

it('lists repositories', function () {

    $this->getRoute('settings')
        ->assertSuccessful()
        ->assertSeeInOrder([
            'Poll by most recent push',
            'foo/bar',
            'baz/zah',
        ]);
});

it('can reset selection from context menu')->todo();
it('can clear caches from context menu')->todo();
it('can delete runs from context menu')->todo();
it('can logout from context menu')->todo();
