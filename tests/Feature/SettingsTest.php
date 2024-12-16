<?php

beforeEach()->login();

beforeEach()->mockRepos(['foo/bar', 'baz/zah']);

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
