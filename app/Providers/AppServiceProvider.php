<?php

namespace App\Providers;

use Dedoc\Scramble\Scramble;
use Illuminate\Support\ServiceProvider;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;


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
        Scramble::configure()
            ->withDocumentTransformers(function (OpenApi $openApi) {
                // Konfigurasi untuk Sanctum
                $openApi->secure(
                    SecurityScheme::http('bearer', 'bearer')
                );
            });
    }
}
