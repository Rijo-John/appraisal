<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\Microsoft\Provider as MicrosoftProvider;

//use SocialiteProviders\Microsoft\MicrosoftExtendSocialite;

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
    public function boot()
    {
        $this->app->extend('Laravel\Socialite\Contracts\Factory', function ($socialite) {
            $socialite->extend('microsoft', function ($app) use ($socialite) {
                return $socialite->buildProvider(MicrosoftProvider::class, config('services.microsoft'));
            });

            return $socialite;
        });

    }
}
