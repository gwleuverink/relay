<?php

namespace App\Providers;

use Native\Laravel\Facades\Menu;
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
        // app()->setLocale(
        //     System::timezone()
        // );

        MenuBar::create()
            ->route('watcher')
            ->vibrancy('light')
            ->resizable(false)
            ->alwaysOnTop(
                config('app.debug')
            )
            ->height(
                28 + (4 * 81)
            )
            ->width(340)
            ->withContextMenu(
                Menu::make(
                    Menu::about(),
                    Menu::separator(),
                    Menu::link('https://github.com/sponsors/gwleuverink', 'Become a Sponsor  ♥️')->openInBrowser(),
                    Menu::separator(),
                    Menu::quit()
                )
            );
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
