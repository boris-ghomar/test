<?php

namespace App\Http\Middleware\HHH\general\auth;

use App\Enums\Routes\SitePublicRoutesEnum;
use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\JsonResponseHelper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BackOfficeUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {

            /**
             * @var App\Models\User $user
             */
            $user =  Auth::user();

            if (!$user->isPersonnel()) {

                if ($request->is('api/*')) {

                    return JsonResponseHelper::errorResponse(null, trans('auth.custom.AccessDenied'), HttpResponseStatusCode::Forbidden->value, trans('auth.custom.AccessDenied', [], 'en'));
                } else {
                    return redirect(SitePublicRoutesEnum::MainPage->route());
                }
            }
        }

        return $next($request);
    }
}
