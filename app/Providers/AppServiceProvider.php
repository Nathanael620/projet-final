<?php

namespace App\Providers;

use App\Services\FAQIntelligenceService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(FAQIntelligenceService::class, function ($app) {
            return new FAQIntelligenceService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
