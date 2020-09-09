<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use GuzzleHttp\Client as HttpClient;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(\Tymon\JWTAuth\Providers\LumenServiceProvider::class);

        $this->app->bind(HttpClient::class, function ($app) {
            return new HttpClient();
        });

        $this->app->bind(
            App\Services\Contracts\AutotracServiceInterface::class,
            App\Services\AutotracService::class
        );
    }
}
