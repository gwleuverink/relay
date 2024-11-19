<?php

namespace App\Providers;

use Native\Laravel\Contracts\ProvidesPhpIni;
use Native\Laravel\Facades\MenuBar;
use Native\Laravel\Facades\Window;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */
    public function boot(): void
    {
        MenuBar::create()
            ->vibrancy('light');

        // Window::create();
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
