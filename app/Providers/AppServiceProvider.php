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
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // manually alias the role middleware
        Route::aliasMiddleware('role', RoleMiddleware::class);
                Schema::defaultStringLength(191);

    }
}
