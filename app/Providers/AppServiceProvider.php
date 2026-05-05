<?php

namespace App\Providers;

use Illuminate\Foundation\Console\ServeCommand;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (PHP_OS_FAMILY !== 'Windows') {
            return;
        }

        ServeCommand::$passthroughVariables = array_values(array_unique(array_merge(
            ServeCommand::$passthroughVariables,
            [
                'APPDATA',
                'COMSPEC',
                'HOMEDRIVE',
                'HOMEPATH',
                'LOCALAPPDATA',
                'PATH',
                'Path',
                'PATHEXT',
                'ProgramData',
                'ProgramFiles',
                'ProgramFiles(x86)',
                'SystemDrive',
                'SystemRoot',
                'TEMP',
                'TMP',
                'USERPROFILE',
                'WINDIR',
                'windir',
            ]
        )));
    }
}
