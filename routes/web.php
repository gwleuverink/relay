<?php

use App\Http\Middleware\Authenticated;
use App\Livewire;
use Illuminate\Support\Facades\Route;

Route::get('login', Livewire\Auth::class)->name('login');

Route::middleware(Authenticated::class)->group(function () {

    Route::get('/', Livewire\Watcher::class)->name('watcher');
});
