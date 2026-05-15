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
        $this->configureRateLimiters();

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

    private function configureRateLimiters(): void
    {
        foreach (['public', 'api', 'admin', 'payment', 'upload', 'chatbot', 'webhook'] as $name) {
            RateLimiter::for($name, function (Request $request) use ($name) {
                return $this->limit($name)->by($this->rateLimitKey($request, $name));
            });
        }

        RateLimiter::for('auth', function (Request $request) {
            $login = strtolower((string) ($request->input('email') ?: $request->input('username') ?: $request->query('email')));

            return $this->limit('auth')->by($this->rateLimitKey($request, 'auth') . '|' . $login);
        });
    }

    private function limit(string $name): Limit
    {
        $max = max(1, (int) config("security.rate_limits.{$name}.max_attempts", 60));
        $decay = max(1, (int) config("security.rate_limits.{$name}.decay_minutes", 1));

        return Limit::perMinute($max, $decay)->response(function (Request $request, array $headers) {
            $message = 'Too many requests. Please wait a moment before trying again.';

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 429, $headers);
            }

            return back()
                ->with('error', $message)
                ->withHeaders($headers);
        });
    }

    private function rateLimitKey(Request $request, string $name): string
    {
        $userId = optional($request->user())->getAuthIdentifier();

        return implode('|', [
            $name,
            $userId ?: 'guest',
            $request->ip() ?: 'unknown-ip',
        ]);
    }
}
