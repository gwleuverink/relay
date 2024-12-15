<?php

use App\Models\WorkflowRun;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\Authenticated;

beforeEach()->fakeHttp();

beforeEach(function () {
    Route::get('guarded', fn () => 'ok')->middleware(Authenticated::class);
});

it('redirects to authentication screen')
    ->get('guarded')
    ->assertRedirectToRoute('login');

it('allows access when authenticated')
    ->login()
    ->get('guarded')
    ->assertSuccessful();

describe('authenticated', function () {

    beforeEach()
        ->login()
        ->fakeHttp();

    it('can visit watcher route')
        ->getRoute('watcher')
        ->assertSuccessful();

    it('can visit settings route')
        ->getRoute('settings')
        ->assertSuccessful();

    it('can visit detail-window route')
        ->defer(fn () => WorkflowRun::factory()->create())
        ->getRoute('detail-window', ['run' => 1])
        ->assertSuccessful();
});

describe('unauthenticated', function () {

    beforeEach()->fakeHttp();

    it('cant visit watcher route')
        ->getRoute('watcher')
        ->assertRedirectToRoute('login');

    it('cant visit settings route')
        ->getRoute('settings')
        ->assertRedirectToRoute('login');

    it('cant visit detail-window route')
        ->defer(fn () => WorkflowRun::factory()->create())
        ->getRoute('detail-window', ['run' => 1])
        ->assertRedirectToRoute('login');
});
