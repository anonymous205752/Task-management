<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;



class AppServiceProvider extends ServiceProvider
{
  public function boot()
{
    if (app()->runningInConsole()) {
        return; // Skip DB checks during build
    }

    // Optional: check if DB is available
    try {
        DB::connection()->getPdo();
    } catch (\Exception $e) {
        // Log or skip silently
    }
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
