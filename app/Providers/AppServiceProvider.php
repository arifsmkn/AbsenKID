<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
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
        // Force HTTPS di production (Cloudflare reverse proxy)
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // Make Setting model available in all Blade views
        View::share('Setting', new Setting());
    }
}
