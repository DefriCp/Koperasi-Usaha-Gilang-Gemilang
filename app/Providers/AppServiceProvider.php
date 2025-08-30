<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /**
         * Paksa generate URL menjadi https ketika:
         * - request memang sudah secure (HTTPS), atau
         * - berada di balik proxy (ngrok/Cloudflare) yang mengirim header X-Forwarded-Proto:https, atau
         * - kamu mengeset FORCE_HTTPS=true di .env (opsional).
         */
        $forwardedProtoIsHttps = request()->header('X-Forwarded-Proto') === 'https';

        if ($forwardedProtoIsHttps || request()->isSecure() || env('FORCE_HTTPS', false)) {
            URL::forceScheme('https');
        }
    }
}
