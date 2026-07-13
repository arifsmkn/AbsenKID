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
        // Force HTTPS di production, tapi hanya kalau request memang masuk lewat
        // Cloudflare (proxy https->http). Akses langsung via IP/http tetap http,
        // supaya asset CSS/JS tidak dipaksa https saat tidak ada TLS di origin.
        if (config('app.env') === 'production' && request()->header('X-Forwarded-Proto') === 'https') {
            URL::forceScheme('https');
        }

        // Make Setting model available in all Blade views
        View::share('Setting', new Setting());
    }
}
