<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\PostRepositoryInterface;
use App\Repositories\PostRepository;
use App\Interfaces\ProfileRepositoryInterface;
use App\Repositories\ProfileRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
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
     * @return void
     */
    public function boot()
    {
        //
    }
}
