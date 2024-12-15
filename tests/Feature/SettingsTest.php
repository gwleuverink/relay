<?php

use App\Support\GitHub\Contracts\GitHub;

beforeEach()->login();

it('lists repositories', function () {
    $this->mock(GitHub::class)->shouldReceive('repos')->andReturn(collect([
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
    ]));

    $this->getRoute('settings')
        ->assertSuccessful()
        ->assertSeeInOrder([
            'Poll by most recent push',
            'foo/bar',
            'baz/zah',
        ]);
});
