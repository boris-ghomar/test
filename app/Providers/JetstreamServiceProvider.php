<?php

namespace App\Providers;

use App\Actions\Jetstream\DeleteUser;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Laravel\Jetstream\Jetstream;

class JetstreamServiceProvider extends ServiceProvider
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
        $this->configurePermissions();

        Jetstream::deleteUsersUsing(DeleteUser::class);

        /******************* HHHE ************************/
        Fortify::loginView(function () {
            return view('hhh.BackOffice.pages.auth.login');
        });

        Fortify::requestPasswordResetLinkView(function () {
            //Forgot password
            return view('hhh.BackOffice.pages.auth.forgot-password');
        });

        Fortify::resetPasswordView(function (Request $request) {
            // return view('auth.reset-password');
            return view('hhh.BackOffice.pages.auth.reset-password', ["request" => $request]);
        });

        /******************* HHHE END ************************/
    }

    /**
     * Configure the permissions that are available within the application.
     */
    protected function configurePermissions(): void
    {
        Jetstream::defaultApiTokenPermissions(['read']);

        Jetstream::permissions([
            'create',
            'read',
            'update',
            'delete',
        ]);
    }
}
