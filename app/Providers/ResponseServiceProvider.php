<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Response\ResponseFactory;
use App\Services\Response\ResponseInterface;

class ResponseServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->bind(ResponseInterface::class, function ($app) {
            return ResponseFactory::make($app['request']);
        });
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }
}
