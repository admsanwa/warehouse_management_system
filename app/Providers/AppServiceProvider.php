<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // \URL::forceScheme('http');
        // \URL::forceScheme('https');

        if (app()->environment('production')) {
            \URL::forceScheme('https');
        }

        // if (env('APP_ENV') !== 'local') {
        //     \URL::forceScheme('https');
        // }

        // if (app()->environment('production')) {
        //     \URL::forceScheme('https');
        // }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();

        \URL::forceScheme('http');
    }
}
