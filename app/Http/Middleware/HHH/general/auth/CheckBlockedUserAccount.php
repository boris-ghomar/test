<?php

namespace App\Http\Middleware\HHH\general\auth;

use App\Enums\Database\Tables\UsersTableEnum;
use App\Enums\Routes\AdminPublicRoutesEnum;
use App\Enums\Routes\SitePublicRoutesEnum;
use App\Enums\Users\UsersStatusEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Enums\SessionEnum as SessionEnumSwarmApi;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckBlockedUserAccount
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

            if (!$user->isActive() && !$user->isSuperAdmin()) {

                $redirect = null;

                if ($user->isPersonnel()) {

                    $redirect = redirect(AdminPublicRoutesEnum::Login->route());
                } else {
                    SessionEnumSwarmApi::deleteSessions(true);
                    $redirect = redirect(SitePublicRoutesEnum::DefaultLogin()->route());
                }

                $accoutStatus = $user[UsersTableEnum::Status->dbName()];

                if ($accoutStatus !== UsersStatusEnum::Active->name)
                    $error = __('auth.custom.AccountStatusMessage', ['status' => UsersStatusEnum::getCase($accoutStatus)->translate()]);
                else
                    $error = __('auth.custom.AccessDenied');


                Auth::logout();
                return $redirect->withErrors([$error]);
            }
        }

        return $next($request);
    }
}
