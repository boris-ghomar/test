<?php

namespace App\Http\Middleware\HHH\general\auth;

use App\Enums\Routes\AdminPublicRoutesEnum;
use App\Enums\Routes\SitePublicRoutesEnum;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class GeustUser
{
    /**
     * Handle an incoming request.
     * Redirect logged user to home, only guest user can go to next
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @var \App\Models\User $test
     */
    public function handle(Request $request, Closure $next): Response
    {

        if (Auth::check()) {

            /**
             * @var App\Models\User $user
             */
            $user =  Auth::user();

            if ($user->isPersonnel())
                return redirect(AdminPublicRoutesEnum::Dashboard->route());
            else
                return redirect(SitePublicRoutesEnum::MainPage->route());
        }

        return $next($request);
    }
}
