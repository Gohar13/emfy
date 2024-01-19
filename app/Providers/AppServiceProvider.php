<?php

namespace App\Providers;

use AmoCRM\Client\AmoCRMApiClient;
use App\Services\AmoCrmService;
use App\Services\CrmServiceInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(AmoCRMApiClient::class, function ($app) {
            return new AmoCRMApiClient(config('amo.client_id'), config('amo.client_secret'),config('amo.redirect_uri'));
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
