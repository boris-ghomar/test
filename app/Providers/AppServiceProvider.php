<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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
        /******************* HHHE ********************/

        /**
         * let's add /app/HHH_Library via namespace
         * We add define a custom namespace for easier usag and complie blade views in this path
         * or other usage
         *
         * Example: You can access to view in this path with below command:
         * view("HHH_Library::jsGrid/js_grid", ["jsGridConfig" => $jsGridConfig]);
         */

         view()->addNamespace("HHH_Library", app_path("/HHH_Library"));

         /**
          * force urls to use SSL https
          */
         if (config('app.APP_SSL'))
             URL::forceScheme('https');

         /******************* HHHE END ********************/
    }
}
