<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Console\ServeCommand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\SiteSetting;

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
        RateLimiter::for('chatbot', function (Request $request) {
            return Limit::perMinute(12)->by(
                optional($request->user())->getAuthIdentifier() ?: $request->ip()
            );
        });

        // Expose site settings to every view as $siteSettings
        View::composer('*', function ($view) {
            try {
                $view->with('siteSettings', SiteSetting::all_keyed());
            } catch (\Throwable $e) {
                // Before migrations run, fall back silently.
                $view->with('siteSettings', []);
            }
        });

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
