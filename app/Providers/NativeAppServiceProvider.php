<?php

namespace App\Providers;

use Native\Laravel\Facades\MenuBar;
use Native\Laravel\Contracts\ProvidesPhpIni;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */
    public function boot(): void
    {
        MenuBar::create('main')
            ->route('watcher')
            ->vibrancy('light')
            ->resizable(false)
            ->alwaysOnTop(
                config('app.debug')
            )
            ->height(
                28 + (4 * 81)
            )
            ->width(340);
    }

    /**
     * Return an array of php.ini directives to be set.
     */
    public function phpIni(): array
    {
        return [
            //
        ];
    }
}
