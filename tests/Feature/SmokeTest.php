<?php

beforeEach()->fakeHttp();

it('redirects to authentication screen')
    ->getRoute('watcher')
    ->assertRedirectToRoute('login');

it('allows access when authenticated')
    ->login()
    ->getRoute('watcher')
    ->assertSuccessful();

describe('authenticated', function () {

    beforeEach()->login();
    beforeEach()->fakeHttp();

    it('can visit settings route')
        ->getRoute('settings')
        ->assertSuccessful();
});
