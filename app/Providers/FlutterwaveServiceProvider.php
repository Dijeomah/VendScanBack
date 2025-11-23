<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Flutterwave\Flutterwave;
use Flutterwave\Helper\Config;

class FlutterwaveServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('flutterwave', function ($app) {
            $config = Config::setUp(
                config('flutterwave.secretKey'),
                config('flutterwave.publicKey'),
                config('flutterwave.encryptionKey'),
                config('flutterwave.env')
            );

            Flutterwave::bootstrap($config);

            return new Flutterwave();
        });
    }

    public function boot(): void
    {
        //
    }
}
