<?php

namespace App\Providers;

use AmoCRM\Client\AmoCRMApiClient;
use Illuminate\Support\ServiceProvider;

class AmoServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(AmoCRMApiClient::class, function ($app) {
            return new AmoCRMApiClient(config('amo.client_id'), config('amo.client_secret'),config('amo.redirect_uri'));
        });
    }
}
