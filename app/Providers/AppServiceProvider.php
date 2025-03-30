<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\TaskServiceInterface;
use App\Services\TaskService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // linking the TaskServiceInterface to the TaskService implementation
        $this->app->bind(TaskServiceInterface::class, TaskService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
