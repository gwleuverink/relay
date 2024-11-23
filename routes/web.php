<?php

use App\Http\Middleware\Authenticated;
use App\Livewire;
use Illuminate\Support\Facades\Route;

Route::get('login', Livewire\Auth::class)->name('login');

Route::middleware(Authenticated::class)->group(function () {

    Route::get('/', Livewire\Watcher::class)->name('watcher');
    Route::get('/run/{run}', Livewire\RunDetail::class)->name('run-detail');
    Route::get('/settings', Livewire\Settings::class)->name('settings');
});
