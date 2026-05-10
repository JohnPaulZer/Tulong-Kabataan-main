<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Console\ServeCommand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\SiteSetting;
use App\Observers\ChatbotKnowledgeObserver;
use App\Services\Chatbot\TulongKabataanKnowledgeService;

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

        // Keep chatbot knowledge fresh: whenever any watched user-side model is
        // created/updated/deleted, the cached snapshot is invalidated and the next
        // chat call will rebuild it with the latest data.
        foreach (TulongKabataanKnowledgeService::WATCHED_MODELS as $modelClass) {
            if (class_exists($modelClass)) {
                $modelClass::observe(ChatbotKnowledgeObserver::class);
            }
        }

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
