<?php

namespace App\Providers;

use App\Services\InfolistService;
use Illuminate\Support\ServiceProvider;

/**
 * Service provider for InfolistService.
 * 
 * Registers the InfolistService as a singleton in the Laravel container
 * for proper dependency injection.
 */
class InfolistServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(InfolistService::class, function ($app) {
            return new InfolistService();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }
}
