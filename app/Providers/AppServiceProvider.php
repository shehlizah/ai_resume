<?php

namespace App\Providers;
use Illuminate\Support\Facades\Schema; // ✅ ADD THIS LINE

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register Google Translate Service as singleton
        $this->app->singleton(\App\Services\GoogleTranslateService::class, function ($app) {
            return new \App\Services\GoogleTranslateService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Manually alias the role middleware
        Route::aliasMiddleware('role', RoleMiddleware::class);
        Schema::defaultStringLength(191);

        // Register Blade directives for translation
        $this->registerBladeDirectives();
    }

    /**
     * Register custom Blade directives
     */
    private function registerBladeDirectives(): void
    {
        // @translate('text') directive for easy translation in views
        \Illuminate\Support\Facades\Blade::directive('translate', function ($expression) {
            return "<?php echo app(\App\Services\GoogleTranslateService::class)->translate({$expression}, app()->getLocale()); ?>";
        });

        // @t('text') short alias
        \Illuminate\Support\Facades\Blade::directive('t', function ($expression) {
            return "<?php echo app(\App\Services\GoogleTranslateService::class)->translate({$expression}, app()->getLocale()); ?>";
        });
    }
}
