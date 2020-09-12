<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use GuzzleHttp\Client as HttpClient;
use App\Services\AutotracService;
use App\Services\SascarService;

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

        $this->app->bind(AutotracService::class, function ($app) {
            return new AutotracService();
        });

        $this->app->bind(SascarService::class, function ($app) {
            return new SascarService();
        });
    }
}
