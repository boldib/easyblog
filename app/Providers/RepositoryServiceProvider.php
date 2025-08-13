<?php

namespace App\Providers;

use App\Interfaces\PostRepositoryInterface;
use App\Interfaces\ProfileRepositoryInterface;
use App\Repositories\PostRepository;
use App\Repositories\ProfileRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     */
    public function register()
    {
        $this->app->bind(
            PostRepositoryInterface::class,
            PostRepository::class,
        );

        $this->app->bind(
            ProfileRepositoryInterface::class,
            ProfileRepository::class,
        );
    }

    /**
     * Bootstrap services.
     *
     */
    public function boot()
    {
        //
    }
}
