<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;



class AppServiceProvider extends ServiceProvider
{
    public function boot()
{
    if (app()->environment('production')) {
        // Run migrations automatically on Railway
        Artisan::call('migrate', ['--force' => true]);
    }

    Schema::defaultStringLength(191); // Safe default for older MySQL
}

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
    
}
