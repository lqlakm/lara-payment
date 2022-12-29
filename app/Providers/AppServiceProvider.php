<?php

namespace App\Providers;

use App\Services\Contracts\PaymentService;
use App\Services\PaypalPaymentService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(PaymentService::class, PaypalPaymentService::class);
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
