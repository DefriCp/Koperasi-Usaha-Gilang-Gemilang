<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Routing\UrlGenerator;

class AppServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        //
    }

 
    public function boot(UrlGenerator $url): void
    {

        if (
            app()->environment('production') ||
            request()->header('X-Forwarded-Proto') === 'https' ||
            env('FORCE_HTTPS', false)
        ) {
            $url->forceScheme('https');
            URL::forceScheme('https'); // optional; salah satu juga cukup
        }
    }
}
