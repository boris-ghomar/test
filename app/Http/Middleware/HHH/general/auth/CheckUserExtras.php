<?php

namespace App\Http\Middleware\HHH\general\auth;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserExtras
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

            /** @var User */
            $user = $request->user();

            $userNotAuthorized = false;
            if ($user->isPersonnel()) {

                if (is_null($user->personnel->personnelExtra))
                    $userNotAuthorized = true;
            } else {

                if (is_null($user->userBetconstruct->betconstructClient))
                    $userNotAuthorized = true;
            }

            if($userNotAuthorized){
                Auth::logout();
            }
        }

        return $next($request);
    }
}
